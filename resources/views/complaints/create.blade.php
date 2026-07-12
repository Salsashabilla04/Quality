@extends('layouts.app')
@section('title', 'Input Complaint Baru')
@section('subtitle', 'Tambah data Customer Complaint / NCR — hasil Apriori & grafik akan otomatis ter-update')

@section('content')
    @include('complaints._form', ['action' => route('complaints.store')])
@endsection
