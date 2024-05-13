<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" href="/css/primer.css" media="screen"/>
    <link rel="stylesheet" href="/css/rec.css" media="screen"/>
    <link rel="stylesheet" href="/css/extra.css" media="screen"/>
    <link rel="stylesheet" href="/css/owl.css" media="screen"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <title>Taxonomy</title>


</head>

<body>
<div class="container">
    @include('vocabulary.head')
    <!--INTRODUCTION SECTION-->
    @include('vocabulary.introduction')
    <!--OVERVIEW SECTION-->
    @include('vocabulary.overview')
    <!--DESCRIPTION SECTION-->
    @include('vocabulary.description')

    <!--CROSSREF SECTION-->
    <div id="crossref"><h2 id="crossreference" class="list">
            Cross-reference for triplo classes, object properties and data properties <span class="backlink"> back to <a
                        href="#toc">ToC</a></span></h2>
        This section provides details for each class and property defined by triplo.
        @if(isset($classes))
            @include('vocabulary.class')
        @endif

        @if(isset($objectProperties))
            @include('vocabulary.objectProperties')
        @endif

        @if(isset($individuals))
            @include('vocabulary.individuals')
        @endif

        @if(isset($annotations))
            @include('vocabulary.annotations')
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

