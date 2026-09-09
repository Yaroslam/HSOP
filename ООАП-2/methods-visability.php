<?php

//1 Публичен в А публичен в Б
class A { public function foo(): string { return 'A::foo'; } }
class B extends A { public function foo(): string { return 'B::foo'; } }


//2 публичен в А небубличен в Б
class A { public function foo() {} }
class B extends A { protected function foo() {} }


//3 непубличен в А публичен в Б
class A { protected function foo(): string { return 'A::foo'; } }
class B extends A { public function foo(): string { return 'B::foo -> ' . parent::foo(); } }


//4 скрыт в А и в Б
class A {
    protected function foo(): string { return 'A::foo'; }
}
class B extends A { protected function foo(): string { return 'B::foo'; } }