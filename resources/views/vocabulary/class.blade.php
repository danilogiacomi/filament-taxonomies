<div xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="classes">
    <h3 id="classes-headline" class="list">Classes</h3>
        <ul class="hlist">
            @foreach ($classes as $class)
                        <li>
                            <a href="#{{$class["label"]}}"
                               title="{{$class["uri"]}}">
                                <span>{{$class["label"]}}</span>
                            </a>
                        </li>
            @endforeach
        </ul>
    @foreach ($classes as $class)
            <div class="entity" id="{{$class["label"]}}">
                <h3>{{$class["label"]}}<sup class="type-c" title="class">c</sup>
                    <span class="backlink"> back to <a href="#toc">ToC</a> or <a href="#classes">Class ToC</a>
             </span>
                </h3>
                <p>
                    <strong>IRI:</strong>{{$class["uri"]}}</p>
{{--                <div class="comment">--}}
{{--                    <span class="markdown">A brief, comprehensive summary of the contents of a document</span>--}}
{{--                </div>--}}
                <dl class="definedBy">
                    <dt>Is defined by</dt>
                    <dd><a target="_blank" href="{{$class["uri"]}}">{{$class["ontology"]}}</a></dd>
                </dl>
{{--                <dl class="description">--}}
{{--                    <dt>has super-classes</dt>--}}
{{--                    <dd/>--}}
{{--                </dl>--}}
            </div>
    @endforeach
</div>
