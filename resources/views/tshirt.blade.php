@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
          <t-shirt-builder-component
            :monster ="{{ $monster }}"
            :quantity = "1"
            :address = "{}">
          </t-shirt-builder-component>
        </div>
    </div>
</div>
@endsection