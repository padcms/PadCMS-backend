Examples:<br/>
<p>
{literal}
{"method":"client.getIssues","params":{"iApplicationId":1},"id":1}
{/literal}
</p>
<br/>
Query:<br/>
<textarea id="query" rows="10" cols="90">
{literal}
{"method":"client.getIssues","params":{"iApplicationId":1},"id":1}
{/literal}
</textarea>

<br/>
<button id="go">Go!</button>

<p>
<a href="http://jsonlint.com/">jsonlint.com</a>
</p>

<div id="result">Result...<div>

<script type="text/javascript" src="{$baseUrl}/js/lib/jquery/jquery-1.7.1.min.js"></script>

{literal}

<script type="text/javascript">

$(document).ready(function(){
    $('#go').click(function(){
        $.ajax({
            type: 'POST',
            url: '/api/v1/jsonrpc.php',
            dataType : "html",
            data: $('#query').val(),
            success: function (data, textStatus) {
                console.log(data);
                $('#result').html('<pre>' + data + '</pre>');
            }
        });
    });
});

</script>

{/literal}