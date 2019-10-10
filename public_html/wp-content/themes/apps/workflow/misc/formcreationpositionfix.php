<br>
<p>
    The following tool can be used to update old forms that do not have the Current Position feature. This feature allows the form creator to move fields to a specific location instead of having to keep clicking move up or move down.
</p>
<p>Instructions:<br>
    <ol>
        <li>In the database, go to the <b>workflow</b> table</li>
        <li>Find the copied, draft form and copy the <b>SAVED_FIELDS</b> column data</li>
        <li>Paste the text into the first text area</li>
        <li>Enter the start and end positions that you want to update (usually from 0 to the last missing position. If the draft has Current Position 34, enter in 33)</li>
        <li>Copy the output text back into the database field <b>SAVED_FIELDS</b></li>
    </ol>
</p>
<b>Enter draft save data:</b><br>
<textarea id="original" rows=20 style="width:100%;" onkeyup="fixStuff()"></textarea><br>

<br>START Position: <input type="number" id="startnumber" onchange="fixStuff()"/><br>
END Position: <input type="number" id="endnumber" onchange="fixStuff()"/><br><br>
<b>Copy fixed draft save data to database</b>
<textarea id="output" rows=20 style="width:100%;"></textarea><br><br>

<script type="text/javascript">
    function fixStuff() {
        let origObj = document.getElementById('original');
        let start = document.getElementById('startnumber').value;
        let end = document.getElementById('endnumber').value;
        let draftText = origObj.value;
        
        for(let i = start; i <= end; i++) {
            let searchString = 'removeField('+i+');">Remove</button></div><div class="clear"></div><div class="workflow workflowboth" style="margin-top: 0px;"><hr style="border-width:4px;"></div><div class="clear"></div></div>';
            
            let replacementString = 'removeField('+i+');">Remove</button></div><div class="clear"></div><div class="workflow workflowleft">New Position:<br>(Current: '+i+')</div><div class="workflow workflowright style-1"><button type="button" style="width: 150px;" onclick="quickSwap('+i+');">Move To Position</button>&nbsp;<input id="quickswap'+i+'" type="number" style="width: 100px;"></div><div class="clear"></div><div class="workflow workflowboth" style="margin-top: 0px;"><hr style="border-width:4px;"></div><div class="clear"></div></div>';
            
            draftText = draftText.replace(searchString, replacementString);
        }
        let outputObj = document.getElementById('output');
        outputObj.value = draftText;
    }
</script>