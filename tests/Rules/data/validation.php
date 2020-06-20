<?php
namespace Validation;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FooController extends Controller
{
    use ValidatesRequests;

    public function foo(Request $request)
    {
        $this->validate($request, ['foo' => 'required']);
        $this->validateWithBag('fooBag', $request, ['foo' => 'required']);
    }

    public function bar(Request $request)
    {
        $request->validate(['foo' => 'required']);
        $request->validateWithBag('barBag', ['foo' => 'required']);
    }

    public function baz()
    {
        request()->validate(['foo' => 'required']);
        request()->validateWithBag('bazBag', ['foo' => 'required']);
    }
}

// No errors should be reported below

class Dummy extends Controller
{
    public function validate(array $stuff)
    {

    }

    public function validateWithBag(string $bag, array $stuff)
    {

    }

    public function foo(Dummy $dummy)
    {
        $dummy->validate(['foo' => 'required']);
        (new Dummy)->validateWithBag('bazBag', ['foo' => 'required']);
    }
}

function foo(Dummy $dummy)
{
    $dummy->validate(['foo' => 'required']);
    (new Dummy)->validateWithBag('bazBag', ['foo' => 'required']);
}