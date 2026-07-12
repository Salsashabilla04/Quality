@extends('layouts.app')
@section('title', 'Edit Complaint ' . $complaint->no_customer)
@section('subtitle', 'Ubah data Customer Complaint / NCR')

@section('content')
    @include('complaints._form', ['action' => route('complaints.update', $complaint)])
@endsection
