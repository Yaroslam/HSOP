<?php

//наследование
class CustomRequest extends Request
{
    //метод в базовом классе запроса
    public function rules(): array
    {
        return [
            'field1' => 'required',
            'field2' => 'required,integer',
        ];
    }
}

//композиция
class Service
{
    //инжектим логер в сервис
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handle(): void
    {
        $this->logger->log();
    }
}


//полиморфизм
function doSomething(LoggerInterface $logger): void
{
    $logger->log('Something');
}

doSomething(new TelegramLoggger());
doSomething(new FileLogger());

