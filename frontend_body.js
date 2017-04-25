    function helpme(id,msg,title,help) {
        if( document.getElementById(id).parentNode.parentNode.parentNode.parentNode.tagName.toUpperCase()=="TABLE" ){
 // document.getElementById(id).parentNode.tagName!="DIV"){
            var theTableBody = document.getElementById(id).parentNode.parentNode.parentNode.parentNode.tBodies[0];
            var row = 1+document.getElementById(id).parentNode.parentNode.rowIndex;
            if ((theRowOpened == row) && (theTableOpened == theTableBody)) {
                removeRow(theRowOpened, theTableOpened);
                theRowOpened = -1;
            } else {
                if (theRowOpened > 0) {
                    if(theRowOpened<row) row--;
                    removeRow(theRowOpened, theTableOpened);
                }
                insertTableRow(row,msg,title,help,theTableBody);
                theRowOpened = row;
                theTableOpened = theTableBody;
            }
        } else {
            var theLinkDiv=document.getElementById(id).parentNode;
            var theHelpDiv=document.getElementById(id+"HelpDiv");
            if(!theHelpDiv){
                theHelpDiv=document.createElement("div");
                theHelpDiv.id=id+"HelpDiv";
                theHelpDiv.className="HelpDiv";
                var theHelpDivContent=document.createElement("div");
                theHelpDivContent.className="HelpDiv-content";
                var closeSpan=document.createElement("span");
                closeSpan.className="close";
                closeSpan.onclick= function(event){document.getElementById(theHelpDiv.id).style.display="none";};
                var closeCross=document.createTextNode("x ");
                theHelpDiv.appendChild(theHelpDivContent);
                var para=document.createElement("p");
                para.innerHTML=insertInnerHTML(msg,title,help);
                closeSpan.appendChild(closeCross);
                theHelpDivContent.appendChild(closeSpan);
                theHelpDivContent.appendChild(para);
                theLinkDiv.appendChild(theHelpDiv);
            }
            theHelpDiv.style.display="block";
        }
    }
    function insertTableRow(row,msg,title,help,theTableBody) {
        var newCell;
        var newRow = theTableBody.insertRow(row);
        newCell = newRow.insertCell(0);
        newCell = newRow.insertCell(1);
        newCell.colSpan = 2;
        newCell.className = MPFORM_CLASS_PREFIX+"help_box_td";
        newCell.innerHTML = insertInnerHTML(msg,title,help);
    }
    function insertInnerHTML(msg,title,help){
        return '<div class="'
            +MPFORM_CLASS_PREFIX
            +'"help_box_div">'
            +((title)
                ? '<h5 class="'
                    +MPFORM_CLASS_PREFIX
                    +'help_box_h5">'
                    +help
                    +': '
                    +title
                    +'</h5><hr class="'
                    +MPFORM_CLASS_PREFIX
                    +'help_box_hr" noshade="noshade" size="1" />'
                : '')
            +'<h6 class="'
            +MPFORM_CLASS_PREFIX
            +'help_box_h6">'
            +msg
            +'</h6></div>';
    }
    function removeRow(row,theTableBody) {
        theTableBody.deleteRow(row);
    }
