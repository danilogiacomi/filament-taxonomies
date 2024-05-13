<div id="overview"><h2 id="overv" class="list">{{$schema->label ?? ''}}: Overview <span class="backlink"> back to <a href="#toc">ToC</a></span></h2>
    <span class="markdown">This ontology has the following classes and properties.</span>
    <h4>Classes</h4>
    <ul xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" class="hlist">
        @foreach ($classes as $class)
            <li>
                <a href="#{{$class['label']}}"
                   title="{{$class['uri']}}">
                    <span>{{$class['label']}}</span>
                </a>
            </li>
        @endforeach
    </ul>
    @if(isset($objectProperties))
    <h4>Object Properties</h4>
    <ul xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" class="hlist">
    @foreach ($individuals as $individual)

            {{--        <li>--}}
            {{--            <a href="#https://w3id.org/GoTriple/ontology/triplO#hasOriginalLanguage"--}}
            {{--               title="https://w3id.org/GoTriple/ontology/triplO#hasOriginalLanguage">--}}
            {{--                <span>has original language</span>--}}
            {{--            </a>--}}
            {{--        </li>--}}
    @endforeach
    </ul>
    @endif
    <h4>Named Individuals</h4>
    <ul xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" class="hlist">
    @foreach ($individuals as $individual)
            <li>
                <a href="#{{$individual['label']}}"
                   title="{{$individual['label']}}">
                    <span>{{$individual['label']}}</span>
                </a>
            </li>
    @endforeach
    </ul>
</div>
