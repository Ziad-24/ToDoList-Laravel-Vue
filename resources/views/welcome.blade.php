@extends('layouts.app')
@section('content')
 
   <div class=" d-flex flex-column justify-content-center align-items-center w-100 h-100">
       <h1 class="text-white display-4">Vue Js to do app</h1>
       <to-do-component user-id="{{auth()->user()->id}}"></to-do-component>
   </div>



@endsection