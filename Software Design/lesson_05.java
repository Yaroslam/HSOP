//в пхп исправить именно этот пример было бы проблемно
//поэтому исправил исходных джава код
//дедлок возникает так как ресурсы брались не в одном порядке, и в итоге один поток
//ждал пока второй его вернет, а второй ждал пока первый вернет свой ресурс.
//проблема решается тем, что ресурсы берем в работу в одном порядке(при работе с бд часто
//так приходится делать, сортируя записи в локе по айди)

public class DeadlockFixed {

    private static final Object lock1 = new Object();
    private static final Object lock2 = new Object();

    public static void main(String[] args) {
        Thread thread1 = new Thread(() -> {
            synchronized (lock1) {
                System.out.println("Thread 1 acquired lock1");

                try { Thread.sleep(50); }
                catch (InterruptedException e) { e.printStackTrace(); }

                synchronized (lock2) {
                    System.out.println("Thread 1 acquired lock2");
                }
            }
        });

        Thread thread2 = new Thread(() -> {
            synchronized (lock1) {
                System.out.println("Thread 2 acquired lock1");

                try { Thread.sleep(50); }
                catch (InterruptedException e) { e.printStackTrace(); }

                synchronized (lock2) {
                    System.out.println("Thread 2 acquired lock2");
                }
            }
        });

        thread1.start();
        thread2.start();

        try {
            thread1.join();
            thread2.join();
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

        System.out.println("Finished");
    }
}