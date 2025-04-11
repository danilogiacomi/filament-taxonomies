<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" href="/vendor/filament-taxonomies/css/primer.css" media="screen"/>
    <link rel="stylesheet" href="/vendor/filament-taxonomies/css/rec.css" media="screen"/>
    <link rel="stylesheet" href="/vendor/filament-taxonomies/css/extra.css" media="screen"/>
    <link rel="stylesheet" href="/vendor/filament-taxonomies/css/owl.css" media="screen"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <title>Taxonomy</title>
</head>

<body>
<div class="container">
    @include('filament-taxonomies::vocabulary.head')
    <!--INTRODUCTION SECTION-->
    @include('filament-taxonomies::vocabulary.introduction')
    <!--OVERVIEW SECTION-->
    @include('filament-taxonomies::vocabulary.overview')
    <!--DESCRIPTION SECTION-->
    @include('filament-taxonomies::vocabulary.description')

    <!--CROSSREF SECTION-->
    <div id="crossref"><h2 id="crossreference" class="list">
            Cross-reference for triplo classes, object properties and data properties <span class="backlink"> back to <a
                        href="#toc">ToC</a></span></h2>
        This section provides details for each class and property defined by triplo.
        @if(isset($classes))
            @include('filament-taxonomies::vocabulary.class')
        @endif

        @if(isset($objectProperties))
            @include('filament-taxonomies::vocabulary.objectProperties')
        @endif

        @if(isset($individuals))
            @include('filament-taxonomies::vocabulary.individuals')
        @endif

        @if(isset($annotations))
            @include('filament-taxonomies::vocabulary.annotations')
        @endif

    </div>
    <div id="legend">
        <div class="entity">
            <sup class="type-c" title="Classes">c</sup>: Classes <br>
            <sup class="type-op" title="Object Properties">op</sup>: Object Properties <br>
            <sup class="type-dp" title="Data Properties">dp</sup>: Data Properties <br>
            <sup class="type-ni" title="Named Individuals">ni</sup>: Named Individuals
        </div>
    </div>
</div>
</body>

