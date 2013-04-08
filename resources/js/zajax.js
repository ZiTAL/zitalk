/**
 * @author zital
 * 
 * zajax: ajax library
 * license: GPL 3.0
 * version: 1.0.2
 */
 
var zajax = function()
{
    this.page = window.location;                    // page to request the petition, default: the same page (window.location)
    this.method = 'post';                           // ajax method: post or get, post by default
    this.charset = 'utf-8';                         // charset utf-8 by default
    this.response = '';                             // response method, XML, JSON or plain text, plain text by default
    this.query;                                     // GET or POST query separated by & blank for default
    this.async = true;                              // ajax connection method (asyncronous, syncronous), asyncronous by default
    this.getSeparator = '?';                        // pagename and parameters separator ? by default
 
    // request public method
    this.request = function()
    {
        var t = this;
        var a = new xmlhttp();                      // get xmlhttp object
        var b = setHeader(t.method, t.charset);     // set get/post different headers
        setQuery(t);                                // construct get/post different properties                        
        t.onRequest();                              // Method to do before all process
        a.open(t.method, t.page, t.async);          // open ajax petition
        a.setRequestHeader('Content-Type', b);      // set headers ALWAYS after OPEN                        
        getState(t, a);                             // ajax reponse state        
    };
 
    // public methods to redefine: w3schools.com
    this.onRequest = function(){};                  // method to do before all process
    this.onSetUp = function(){};                    // method to do when The request has been set up
    this.onSend = function(){};                     // method to do when The request has been sent
    this.onProcess = function(){};                  // method to do when The request is in process
    this.onComplete = function(t){};                 // method to do when The request is complete
    this.onError = function(){};                    // method to do when The request return error
 
    // private method to set get/post headers
    var setHeader = function(m, c)
    {
        var a = '';
        if(m.toLowerCase()=='post')
            a = a + "application/x-www-form-urlencoded;"; // post Header
        a = a + "charset="+c;
        return a;
    };
 
    // private method set get/post petition properties
    var setQuery = function(t)
    {
        // DEFAULT POST example page = index.php and query = a=hello, php -> $_POST['hello']
        if(t.method!=='post')                       // GET example page = index.php?a=hello and query = '', php -> $_GET['hello']
        {
            t.page = t.page+t.getSeparator+t.query;
            t.query = '';
        }
    };
    
    // private method to set ajax petition state
    var getState = function(t, a)
    {
        if(t.async)
        {
            a.onreadystatechange = function() // get petition changestates
            {
                switch (a.readyState)
				{
                    case 1:									// if readystate is 1 The request has been set up
                        t.onSetUp();
                        break;
                    case 2:									// if readystate is 2 The request has been sent
                        t.onSend();
                        break;
                    case 3:									// if readystate is 3 The request is in process
                        t.onProcess();
                        break;
                    case 4:									// if readystate is 4 the request is complete
                        reqResponse(t, a);
                        break;
                }
            };
            a.send(t.query);                                // send get/post query      
        }
        else
        {
            a.send(t.query);                                // send get/post query      
            console.log(3);
            return false;            
            reqResponse(t, a);
        }
    };
    
    // private method to get ajax petition response 
    var reqResponse = function(t, a)
    {
        if(a.status===200)                                  // if status is 200 petition OK     
            if(t.response.toLowerCase()==='xml')            // XML response
                t.onComplete(a.responseXML);
            else if(t.response.toLowerCase()==='json')      // JSON response
                t.onComplete(eval("("+a.responseText+")"));
            else                                            // plain text response
                t.onComplete(a.responseText);
        else
            t.onError();                                    // if error occurred execute error method
    };
    
    // private method get xmlhttp object
    var xmlhttp = function()
    {
        var a;try{a = new XMLHttpRequest();}
        catch(e){try{a = new ActiveXObject('Msxml2.XMLHTTP');}
        catch(e){try{a = new ActiveXObject('Microsoft.XMLHTTP');}
        catch(e){alert("Your browser doesn't support ajax");return false}
        }}return a;
    };
};