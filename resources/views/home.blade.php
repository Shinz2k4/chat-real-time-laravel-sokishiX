@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 home-container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card home-card fade-in">
                    <div class="card-body home-body p-0" id="app">
                        <chat-app :user="{{ auth()->user() }}"></chat-app>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
