@extends('layouts.app')
@section('page_title', 'Create Gender')
@section('content')
    <div class="container">
        <div class="row">
             <div class="card">
                <div class="card-body">
                    <div class="col-md-6">
                        <form action="{{ route('banners.update', $banner->id) }}" method="POST" class="material-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <input type="text" required="required" name="title" id="banner" value="{{ $banner->title }}"/>
                                <label for="input" class="control-label">Banner Title</label><i class="bar"></i>
                            </div>
                            <div class="form-group">
                                <textarea type="text" name="description" required="required" >{{ $banner->description }}</textarea>
                                <label for="input" class="control-label">Banner Description</label><i class="bar"></i>
                            </div>
                            <div class="form-group">
                                <input type="file" name="photo" class="form-control" />
                                <label for="input" class="control-label">Banner Image</label><i class="bar"></i>
                                <img src="{{ $banner->photo }}" width="50" height="50"/>
                            </div>
{{--                             <div class="form-group">--}}
{{--                                    <label for="active" class="font-weight-medium font-weight-500 flag-color">Status</label>--}}
{{--                                    <div class="row text-right">--}}
{{--                                        <div class="col-4">--}}
{{--                                            <label for="active">Active</label>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-8">--}}
{{--                                            <input type="radio" id="active" name="status" value="1" @if($banner->status == 1) checked @endif required>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row mt-2">--}}
{{--                                        <div class="col-4">--}}
{{--                                            <label for="inactive">Inactive</label>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-8">--}}
{{--                                            <input type="radio" id="inactive" name="status" value="0" @if($banner->status == 0) checked @endif required>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                            </div>--}}
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary" id="saveBtn">Submit</button>
                                <a href="{{route('banners.index')}}" class="btn btn-danger" id="backBtn">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
