package main

import (
	"fmt"
	"sync"
	"sync/atomic"
)

var counter int64

func main() {
	var wg sync.WaitGroup

	task := func() {
		defer wg.Done()
		for i := 0; i < 1000; i++ {
			atomic.AddInt64(&counter, 1)
		}
	}

	wg.Add(2)
	go task()
	go task()

	wg.Wait()

	fmt.Println("Counter:", atomic.LoadInt64(&counter))
}