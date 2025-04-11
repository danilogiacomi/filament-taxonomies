<div xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="namedindividuals">
    <h3 id="namedindividuals" class="list">Named Individuals</h3>
    <ul class="hlist">
        @foreach ($individuals as $individual)
            <li id="{{$individual['uri']}}">
                <a href="#{{$individual['uri']}}"
                   title="{{$individual['label']}}">
                    <span>{{$individual['label']}}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
@foreach ($individuals as $individual)
    <div class="entity" id={{ $individual['label'] }} href="#{{$individual['label']}}">
        <h3>{{ $individual['label'] }}<sup class="type-ni" title="named individual">ni</sup>
        </h3>
        <p>
            <strong>IRI:</strong> {{ $individual['uri'] }}</p>
        <div class="comment">
            <span class="markdown">{{ $individual['definition'] }}</span>
        </div>
        <dl class="description">
            <dt>belongs to</dt>
            <dd>
                <a href="http://www.w3.org/2004/02/skos/core#Concept" target="_blank" title="http://www.w3.org/2004/02/skos/core#Concept">Concept</a>
                <sup class="type-c" title="class">c</sup>
            </dd>
            <dt>has facts</dt>
            @if(isset($individual['close_match']))
                <dd>
                    <a href="http://www.w3.org/2004/02/skos/core#closeMatch"
                       target="_blank"
                       title="http://www.w3.org/2004/02/skos/core#closeMatch">close match</a>
                    <sup class="type-op" title="object property">op</sup>
                    <span class="literal"><a href={{$individual['close_match']}} target="_blank">{{$individual['close_match']}}</a></span>
                </dd>
            @endif
            @if(isset($individual['exact_match']))
                <dd>
                    <a href="http://www.w3.org/2004/02/skos/core#exactMatch"
                       target="_blank"
                       title="http://www.w3.org/2004/02/skos/core#exactMatch">exact match</a>
                    <sup class="type-op" title="object property">op</sup>
                    <span class="literal"><a href={{$individual['exact_match']}} target="_blank">{{ $individual['exact_match'] }}</a></span>
                </dd>
            @endif
            @if(isset($individual['inSchema']))
                <dd>
                    <a href="http://www.w3.org/2004/02/skos/core#inScheme"
                       target="_blank"
                       title="http://www.w3.org/2004/02/skos/core#inScheme">in schema</a>
                    <sup class="type-op" title="object property">op</sup>
                    <span class="literal"><a href={{$individual['inSchema_uri']}} target="_blank">{{ $individual['inSchema'] }}</a></span>
                </dd>
            @endif
            @if(isset($individual['topConcepts']))
                @foreach ($individual['topConcepts'] as $topConcept)
                    <dd>
                        <a href="http://www.w3.org/2004/02/skos/core#hasTopConcept"
                           target="_blank"
                           title="http://www.w3.org/2004/02/skos/core#hasTopConcept">has top concept</a>
                        <sup class="type-op" title="object property">op</sup>
                        <span class="literal"><a href={{$topConcept->uri}} target="_blank">{{ $topConcept->label }}</a></span>
                    </dd>
                @endforeach
            @endif
            @if(isset($individual['broaders']))
                @foreach ($individual['broaders'] as $broader)
                    <dd>
                        <a href="http://www.w3.org/2004/02/skos/core#broader"
                           target="_blank"
                           title="http://www.w3.org/2004/02/skos/core#broader">broader</a>
                        <sup class="type-op" title="object property">op</sup>
                        <span class="literal"><a href={{$broader->uri}} target="_blank">{{ $broader->label }}</a></span>
                    </dd>
                @endforeach
            @endif
        </dl>
    </div>

@endforeach
