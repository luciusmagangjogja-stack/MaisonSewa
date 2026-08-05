@extends('Layouts.app')

@section('title', 'Dashboard Sales')
@section('page-title', 'Dashboard')
@section('subtitle', 'Selamat datang kembali, ' . auth()->user()->name)

@section('content')
    @php($role = 'sales')
    @include('dashboard._dashboard_common', ['role' => $role])
@endsection

