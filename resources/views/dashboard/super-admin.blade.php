@extends('Layouts.app')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard')
@section('subtitle', 'Selamat datang kembali, ' . auth()->user()->name)

@section('content')
    @php($role = 'super_admin')
    @include('dashboard._dashboard_common', ['role' => $role])
@endsection

