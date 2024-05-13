<div id="annotationproperties">
    <h2>Annotation Properties</h2>
    <ul class="hlist">
    @foreach($annotations as $annotation)
        <li><a href="{{$annotation['uri']}}"
               title="{{$annotation['uri']}}"><span>{{$annotation['label']}}</span></a>
        </li>
    @endforeach
    </ul>
    @foreach($annotations as $annotation)
    <div id="{{$annotation['label']}}" class="entity"><a name="{{$annotation['uri']}}"></a>
        <h3>{{$annotation['label']}}<sup title="annotation property" class="type-ap">ap</sup><span class="backlink"> back to <a href="#toc">ToC</a> or <a href="#annotationproperties">Annotation Property ToC</a></span></h3>
        <p><strong>IRI: </strong><a href="{{$annotation['uri']}}" target="_blank">{{$annotation['uri']}}</a></p>
    </div>
    @endforeach
</div>
