package main

import (
	"fmt"
	"sync"
	"sync/atomic"
	"time"
)

func main() {
	atomicExample()
	mutexExample()
	semaphoreExample()
	rwMutexExample()
	eventsExample()
}

func atomicExample() {
	var counter atomic.Int64
	var wg sync.WaitGroup

	wg.Add(5)

	for i := 0; i < 5; i++ {
		go func() {
			defer wg.Done()

			for j := 0; j < 5; j++ {
				counter.Add(1)
			}
		}()
	}

	wg.Wait()

	fmt.Printf("Результат счетчика: %d\n", counter.Load())
}

type Tables struct {
	mu    sync.Mutex
	seats map[int]int
}

func (t *Tables) takeSeat(visitorID int) (int, bool) {
	t.mu.Lock()
	defer t.mu.Unlock()

	for tableID := 1; tableID <= 10; tableID++ {
		if t.seats[tableID] >= 2 {
			continue
		}

		t.seats[tableID]++

		fmt.Printf(
			"Посетитель %02d сел за стол %02d. Занято: %d/%d\n",
			visitorID,
			tableID,
			t.seats[tableID],
			2,
		)

		return tableID, true
	}

	fmt.Printf(
		"Посетитель %02d: свободных мест нет, ухожу.\n",
		visitorID,
	)

	return 0, false
}

func (t *Tables) leaveSeat(tableID, visitorID int) {
	t.mu.Lock()
	defer t.mu.Unlock()

	t.seats[tableID]--

	fmt.Printf(
		"Посетитель %02d ушёл из-за стола %02d. Занято: %d/%d\n",
		visitorID,
		tableID,
		t.seats[tableID],
		2,
	)
}

func (t *Tables) isSeatTaken(seatInd int) bool {
	t.mu.Lock()
	defer t.mu.Unlock()
	if t.seats[seatInd] > 2 {
		return true
	}

	return false
}

func mutexExample() {
	const visitors = 25

	tables := Tables{
		seats: make(map[int]int, 10),
	}

	var wg sync.WaitGroup
	wg.Add(visitors)

	for visitorID := 1; visitorID <= visitors; visitorID++ {
		go func(id int) {
			defer wg.Done()

			tableID, ok := tables.takeSeat(id)
			if !ok {
				return
			}

			defer tables.leaveSeat(tableID, id)

			time.Sleep(500 * time.Millisecond)
		}(visitorID)
	}

	wg.Wait()

	fmt.Println("\nВсе посетители ушли.")
}

func semaphoreExample() {

	semaphore := make(chan struct{}, 3)

	var wg sync.WaitGroup
	wg.Add(5)

	for carID := 1; carID <= 5; carID++ {
		go func(id int) {
			defer wg.Done()
			fmt.Printf("Машина %02d ожидает парковки\n", id)
			semaphore <- struct{}{}

			defer func() {
				<-semaphore
			}()

			fmt.Printf("Машина %02d въехала на парковку\n", id)
			time.Sleep(time.Second)
			fmt.Printf("Машина %02d выехала с парковки\n", id)
		}(carID)
	}

	wg.Wait()
	fmt.Println("Все машины завершили парковку")
}

type Catalog struct {
	mu     sync.RWMutex
	prices map[string]int
}

func (c *Catalog) GetPrice(product string) int {
	c.mu.RLock()
	defer c.mu.RUnlock()
	price, _ := c.prices[product]
	return price
}

func (c *Catalog) SetPrice(product string, price int) {
	c.mu.Lock()
	defer c.mu.Unlock()
	c.prices[product] = price
}

func rwMutexExample() {
	catalog := Catalog{
		prices: map[string]int{
			"coffee": 100,
		},
	}

	var wg sync.WaitGroup
	wg.Add(4)

	for readerID := 1; readerID <= 3; readerID++ {
		go func(id int) {
			defer wg.Done()

			for i := 0; i < 4; i++ {
				price := catalog.GetPrice("coffee")

				fmt.Printf(
					"Покупатель %d: цена кофе — %d\n",
					id,
					price,
				)

				time.Sleep(100 * time.Millisecond)
			}
		}(readerID)
	}

	go func() {
		defer wg.Done()

		for _, newPrice := range []int{110, 120, 130} {
			time.Sleep(120 * time.Millisecond)

			catalog.SetPrice("coffee", newPrice)

			fmt.Printf(
				"Администратор изменил цену на %d\n",
				newPrice,
			)
		}
	}()

	wg.Wait()

	finalPrice := catalog.GetPrice("coffee")
	fmt.Println("Итоговая цена:", finalPrice)
}

func eventsExample() {
	levelReady := make(chan struct{})

	var wg sync.WaitGroup
	wg.Add(5)

	for bolidId := 1; bolidId <= 5; bolidId++ {
		go func(id int) {
			defer wg.Done()

			fmt.Printf(
				"Болид %d ожидает старта\n",
				id,
			)

			<-levelReady

			fmt.Printf(
				"Болид %d Стартовал\n",
				id,
			)
		}(bolidId)
	}

	fmt.Println("3 2 1")
	fmt.Println("Старт!")

	close(levelReady)

	wg.Wait()
}
