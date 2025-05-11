<div class="progress" style="height: 20px;">
    <div class="progress-bar 
        @if($percentage == 100) bg-success
        @elseif($percentage >= 50) bg-primary
        @else bg-warning @endif" 
        role="progressbar" 
        style="width: {{ $percentage }}%"
        aria-valuenow="{{ $percentage }}" 
        aria-valuemin="0" 
        aria-valuemax="100">
        {{ floor($percentage) }}%
    </div>
</div>