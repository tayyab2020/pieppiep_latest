@extends('layouts.handyman')

@section('content')

<style>

    /*** FONTS ***/
    @import url(https://fonts.googleapis.com/css?family=Montserrat:900|Raleway:400,400i,700,700i);
    /*** VARIABLES ***/
    /* Colors */
    /*** EXTEND ***/
    /* box-shadow */
    
    ol.gradient-list > li, ol.gradient-list > li::before {
        box-shadow: 0.25rem 0.25rem 0.6rem rgba(0, 0, 0, 0.05), 0 0.5rem 1.125rem rgba(75, 0, 0, 0.05);
    }

    #gradient-main {
        display: block;
        margin: 0 auto;
        padding: 1rem;
        float: none !important;
        width: 50%;
    }

    ol.gradient-list {
        counter-reset: gradient-counter;
        list-style: none;
        margin: 1.75rem 0;
        padding-left: 1rem;
        width: 100%;
    }

    ol.gradient-list > li {
        background: white;
        border-radius: 0 0.5rem 0.5rem 0.5rem;
        counter-increment: gradient-counter;
        margin-top: 1rem;
        min-height: 3rem;
        padding: 1rem 1rem 1rem 3rem;
        position: relative;
        min-width: 100%;
    }
    
    ol.gradient-list > li::before, ol.gradient-list > li::after {
        background: linear-gradient(135deg, #83e4e2 0%, #a2ed56 100%);
        border-radius: 1rem 1rem 0 1rem;
        content: '';
        height: 3rem;
        left: -1rem;
        overflow: hidden;
        position: absolute;
        top: -1rem;
        width: 3rem;
    }

    ol.gradient-list > li::before {
        align-items: flex-end;
        content: counter(gradient-counter);
        color: #1d1f20;
        display: flex;
        font: 900 1.5em/1 'Montserrat';
        justify-content: flex-end;
        padding: 0.125em 0.25em;
        z-index: 1;
    }
    
    ol.gradient-list > li:nth-child(10n + 1):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.2) 0%, rgba(253, 220, 50, 0.2) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 2):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.4) 0%, rgba(253, 220, 50, 0.4) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 3):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.6) 0%, rgba(253, 220, 50, 0.6) 100%);
    }
    
    ol.gradient-list > li:nth-child(10n + 4):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.8) 0%, rgba(253, 220, 50, 0.8) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 5):before {
        background: linear-gradient(135deg, #a2ed56 0%, #fddc32 100%);
    }

    ol.gradient-list > li:nth-child(10n + 6):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.8) 0%, rgba(253, 220, 50, 0.8) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 7):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.6) 0%, rgba(253, 220, 50, 0.6) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 8):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.4) 0%, rgba(253, 220, 50, 0.4) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 9):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.2) 0%, rgba(253, 220, 50, 0.2) 100%);
    }

    ol.gradient-list > li:nth-child(10n + 10):before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0) 0%, rgba(253, 220, 50, 0) 100%);
    }

    ol.gradient-list > li + li {
      margin-top: 2rem;
    }

    #content{ overflow-y: auto; }
    
</style>

<div style="height: 100%;" class="container">

    @if(count($messages) > 0)

        <main id="gradient-main">
            <ol class="gradient-list">
    
                @foreach($messages as $key)

                    <li>{{$key->text}}</li>

                @endforeach
    
            </ol>
        </main>

    @else

        <div style="display: flex;align-items: center;height: 100%;">
            <h2 style="text-align: center;width: 100%;">{{__('text.No messages found....')}}</h2>
        </div>

    @endif
  
</div>

@endsection
