<div class="modal fade" id="iframeEventModal" tabindex="-1" role="dialog"
     aria-labelledby="documents_createDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Iframe Code Copier')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <main class="main-code">
                    <pre class="pre-code"><code class="language-html">
                    &lt;iframe src="{{ route('all-events.index',['name' => getSlugName(auth()->user()->name)]) }}"
                           width="100%" height="300" style="border:none;"&gt;&lt;/iframe&gt;</code></pre>
                </main>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>