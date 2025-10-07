@extends('layouts.app')
@section('page_title', 'User Profile')
@section('content')
    <div class="row">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="border-bottom text-center pb-4">
                                    <img src="{{ Auth::user()->avatar }}" alt="profile" class="img-lg rounded-circle mb-3">
                                    <div class="mb-3">
                                        <h3>{{Auth::user()->name}}</h3>
                                    </div>
                                </div>
                                <div class="py-4">
                                    <p class="clearfix">
                                      <span class="float-start"> Status</span>
                                      <span class="float-end text-muted">{{@Auth::user()->status === 1 ? 'Active' : 'Inactive'}}</span>
                                    </p>
                                    <p class="clearfix">
                                      <span class="float-start">Mail</span>
                                      <span class="float-end text-muted">{{@Auth::user()->email}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
