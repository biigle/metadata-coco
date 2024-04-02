<div class="panel panel-default">
   <div class="panel-heading">
      <a href="{{ route('quotes') }}"><h3 class="panel-title">Inspiring Quote</h3></a>
   </div>
   <div class="panel-body">
      <blockquote>
         {{ Illuminate\Foundation\Inspiring::quote() }}
      </blockquote>
   </div>
</div>
