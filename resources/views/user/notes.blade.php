<div class="appnavbar-body">
    <div class="appnavbar-body-title">
        <!-- Sidebar Header Dropdown Start -->
        <div class="dropdown mr-2">
            <!-- Dropdown Button Start -->
            <button class="btn btn-outline-default dropdown-toggle filter-toggle" type="button" data-notes-filter-list="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__("text.All Notes")}}</button>
            <!-- Dropdown Button End -->

            <!-- Dropdown Menu Start -->
            <div class="dropdown-menu filter-notes">
                <a class="dropdown-item filter-note-item" selected data-notes-filter="" data-select="all-notes" href="javascript:void(0)">{{__("text.All Notes")}}</a>
                @foreach($allData[0] as $note)
                    <a class="dropdown-item filter-note-item" data-notes-filter="" data-select="{{$note->id}}" href="javascript:void(0)">{{$note->title}}</a>
                @endforeach
            </div>
            <!-- Dropdown Menu End -->
        </div>
        <!-- Sidebar Header Dropdown End -->

        <!-- Sidebar Search Start -->
        <form class="form-inline">
            <div class="input-group">
                <input type="text" class="form-control search-notes border-right-0 transparent-bg pr-0" placeholder="{{__('text.Search notes')}}">
                <div class="input-group-append">
                    <div class="input-group-text transparent-bg border-left-0" role="button">
                        <!-- Default :: Inline SVG -->
                        <svg class="text-muted hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>

                        <!-- Alternate :: External File link -->
                        <!-- <img class="injectable hw-20" src="./../assets/media/heroicons/outline/search.svg" alt=""> -->
                    </div>
                </div>
            </div>
        </form>
        <!-- Sidebar Search End -->
    </div>

    <div class="note-container">
        @foreach($allData[0] as $note)

            @include("user.note")

        @endforeach
    </div>
</div>

<div class="appnavbar-footer">
    <button class="btn btn-primary btn-block add-note">{{__('text.Add new note')}}</button>
</div>