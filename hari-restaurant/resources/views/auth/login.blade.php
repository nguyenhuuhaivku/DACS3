@extends('adminlte::auth.login')
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    <?php session()->forget('error'); ?>
@endif
