@extends('layouts.admin_master')

@section('title', 'Profil Admin - Admin Panel')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Profile Header -->
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 3rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h3 class="mb-1">{{ Auth::user()->name }}</h3>
                    <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                    <small class="text-muted">Administrator sejak {{ Auth::user()->created_at->format('M Y') }}</small>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Profil Admin
                    </h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ Auth::user()->name }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-dark w-100">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-box-arrow-right"></i> Logout Admin
                                    </button>
                                </form>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection