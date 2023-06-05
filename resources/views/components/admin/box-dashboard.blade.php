@props(['data', 'title', 'route', 'boxtype', 'icon'])
<div class="small-box bg-{{ $boxtype }}">
    <div class="inner">
      <h3>
        {{ number_format ($data , $decimals = 0 , $dec_point = "," , $thousands_sep = "." ) }}
      </h3>
      <p>{{ $title }}</p>
    </div>
    <div class="icon">
      <i class="ion {{$icon}}"></i>
    </div>
</div>