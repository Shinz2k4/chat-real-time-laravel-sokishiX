@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4 home-container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card home-card fade-in">
                    <div class="card-header home-header text-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="brand-logo me-3">
                                    <div class="logo-circle">
                                        S
                                    </div>
                                </div>
                                <div class="brand-text">
                                    <h2 class="mb-0 fw-bold">
                                        <span class="text-white">Sokishi</span><span class="text-warning">X</span>
                                    </h2>
                                    <small class="text-light opacity-75">Modern Chat Platform</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="online-badge bounce-in">
                                    <i class="fas fa-circle"></i>
                                    Online
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body home-body p-0" id="app">
                        <chat-app :user="{{ auth()->user() }}"></chat-app>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
