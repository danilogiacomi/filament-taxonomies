<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="/css/primer.css" media="screen" />
<link rel="stylesheet" href="/css/rec.css" media="screen" />
<link rel="stylesheet" href="/css/extra.css" media="screen" />
<link rel="stylesheet" href="/css/owl.css" media="screen" />


</head>

<body>

   <div class="entity" id="archaeology_and_prehistory">
      <h3>{{ $individual }}<sup class="type-ni" title="named individual">ni</sup>
      </h3>
      <p>
         <strong>IRI:</strong> {{ $uri }}</p>
      <div class="comment">
         <span class="markdown">{{ $definition }}</span>
      </div>
      <dl class="description">
          <dt>belongs to</dt>
          <dd>
              <a href="http://www.w3.org/2004/02/skos/core#{{$type}}" target="_blank" title="http://www.w3.org/2004/02/skos/core#{{$type}}">{{$type}}</a>
              <sup class="type-c" title="class">c</sup>
          </dd>
          @if(isset($exact_match) | isset($narrow_uri))
          <dt>has facts</dt>
          @if(isset($narrow_uri))
          <dd>
              <a href="http://www.w3.org/2004/02/skos/core#narrower"
                 target="_blank"
                 title="http://www.w3.org/2004/02/skos/core#narrower">narrower</a>
              <sup class="type-op" title="object property">op</sup>
              <span class="literal"><a href={{$narrow_uri}} target="_blank">{{ $narrow_label}}</a></span>
          </dd>
          @endif
          @if(isset($exact_match))
          <dd>
              <a href="http://www.w3.org/2004/02/skos/core#exactMatch"
                 target="_blank"
                 title="http://www.w3.org/2004/02/skos/core#exactMatch">exact match</a>
              <sup class="type-op" title="object property">op</sup>
              <span class="literal"><a href={{$exact_match}} target="_blank">{{ $exact_match }}</a></span>
          </dd>
          @endif
          @if(isset($inSchema))
              <dd>
                  <a href="http://www.w3.org/2004/02/skos/core#inScheme"
                     target="_blank"
                     title="http://www.w3.org/2004/02/skos/core#inScheme">in schema</a>
                  <sup class="type-op" title="object property">op</sup>
                  <span class="literal"><a href={{$inSchema_uri}} target="_blank">{{ $inSchema }}</a></span>
              </dd>
          @endif
          @if(isset($topConcepts))
              @foreach ($topConcepts as $topConcept)
                  <dd>
                      <a href="http://www.w3.org/2004/02/skos/core#hasTopConcept"
                         target="_blank"
                         title="http://www.w3.org/2004/02/skos/core#hasTopConcept">has top concept</a>
                      <sup class="type-op" title="object property">op</sup>
                      <span class="literal"><a href={{$topConcept->uri}} target="_blank">{{ $topConcept->label }}</a></span>
                  </dd>
              @endforeach
          @endif
          @endif
      </dl>
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

