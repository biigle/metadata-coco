@extends('app')
@section('title', 'Inspiring quotes')
@section('content')
<div id="quotes-container" class="container">
   <div class="col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3">
      <blockquote v-text="quote"></blockquote>
      <button class="btn btn-default" v-on:click="refreshQuote">refresh</button>
   </div>
</div>
@endsection

@push('scripts')
<script src="{{ cachebust_asset('vendor/module/scripts/main.js') }}"></script>
@endpush
@push('styles')
<link href="{{ cachebust_asset('vendor/module/styles/main.css') }}" rel="stylesheet">
@endpush
