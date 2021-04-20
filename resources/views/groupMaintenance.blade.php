@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Private Groups</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <ul>
                            <li>
                                Private groups are a way of creating a closed and secure environment for creating monsters. 
                            </li>
                            <li>
                                YOU control who can join your group by providing every artist with the unique group code.
                            </li>
                            <li>
                                They can then join the group by going to the "Private Group" tab on the login page and entering 
                                their name and the unique code. They don't even need to create an account.
                            </li>
                    </div>
                    <groups-grid-component
                        :groups="{{ $groups }}"
                        :user="{{ $user }}"
                        >
                    </groups-grid-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection