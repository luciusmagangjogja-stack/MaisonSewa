@extends('Layouts.app')

@section('title', 'Dashboard Admin Cabang')
@section('page-title', 'Dashboard')
@section('subtitle', 'Selamat datang kembali, ' . auth()->user()->name)

@section('content')
    @php($role = 'admin_toko')
    @include('dashboard._dashboard_common', ['role' => $role])
@endsection

