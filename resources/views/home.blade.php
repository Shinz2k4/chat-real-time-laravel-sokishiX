@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-gradient-primary text-white py-4" style="border: none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="brand-logo me-3">
                                    <div class="logo-circle bg-white text-primary d-flex align-items-center justify-content-center shadow" 
                                         style="width: 50px; height: 50px; border-radius: 50%; font-weight: 900; font-size: 24px; transition: all 0.3s ease;">
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
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                    Online
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0" id="app" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                        <chat-app :user="{{ auth()->user() }}"></chat-app>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
