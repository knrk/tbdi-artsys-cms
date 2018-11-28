/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

var Art_Ajax = {
    classInvalid : 'field-invalid',
    defaultEvent : 'click',
    boundToAjaxClassName: 'bta',
    elements: {},
    token: window.token,
    token_name: window.token_name,
    request_guid: rand_str(32),
    request_guid_name: '_guid',
    request_name: '_name',
    responseWindowPrefix: '.file_uploader_',
    statusIconLoading: '<i class="fa fa-spinner fa-pulse"></i>',
    statusIconLoaded: '<i class="fa fa-check"></i>',
    statusIconError: '<i class="fa fa-exclamation-circle"></i>',
    stopIcon: '<i class="fa fa-ban"></i>',
    $loadingBar : null,
    animation_time: 300,
    status : {
	ok : 1,
	alert : 2,
	error : 3
    },
    
    /**
     *	Call jQuery $.ajax function with prepared options
     */
    call : function(options)
    {
	if(typeof options === "object")
	{
	    options.method = "POST";
	    options.dataType = "json";

	    if( typeof options.data === "object"  && options.data !== null )
	    {
		if( options.data instanceof FormData )
		{
		    if( typeof options.name !== "undefined" )
		    {
			options.data.append(Art_Ajax.request_name,options.name);
		    }
		    options.data.append(Art_Ajax.token_name, Art_Ajax.token);
		    options.data.append(Art_Ajax.request_guid_name, Art_Ajax.request_guid);
		}
		else
		{
		    if( typeof options.name !== "undefined" )
		    {
			options.data[Art_Ajax.request_name] = options.name;
		    }
		    options.data[Art_Ajax.token_name] = Art_Ajax.token;
		    options.data[Art_Ajax.request_guid_name] = Art_Ajax.request_guid;
		}
	    }
	    else if( typeof options.data === "string" )
	    {
		if( typeof options.name !== "undefined" )
		{
		    options.data = options.data+'&'+Art_Ajax.request_name+'='+options.name;
		}
		options.data = options.data+'&'+Art_Ajax.token_name+'='+Art_Ajax.token;
		options.data = options.data+'&'+Art_Ajax.request_guid_name+'='+Art_Ajax.request_guid;
	    }
	    else
	    {
		options.data = "";
		
		if( typeof options.name !== "undefined" )
		{
		    options.data = Art_Ajax.request_name+'='+options.name+'&';
		}
		options.data = options.data+'&'+Art_Ajax.token_name+'='+Art_Ajax.token;
		options.data = options.data+'&'+Art_Ajax.request_guid_name+'='+Art_Ajax.request_guid;
	    }
	    
	    if( typeof options.progress !== "undefined" )
	    {
		options.xhr = function() {
		    var xhr = new window.XMLHttpRequest();
		    (xhr.upload || xhr).addEventListener("progress", function(evt) 
		    {
			if (evt.lengthComputable) 
			{
			    options.progress(evt.loaded*100 / evt.total);
			}
		   }, false);

		   return xhr;
		};
	    }

	    // if( typeof options.error === "undefined" )
	    // {
		// options.error = function(data) {
		//     console.log(data.responseText);
		// };
	    // }
	    
	    return $.ajax(options);
	}
	else
	{
	    console.log('Invalid argument supplied for ajax()');
	}
    },
    
    /**
     *	Add "invalid-class" tag to fields (by name)
     */
    makeFieldsInvalid : function($form,fields)
    {
	for(var key in fields)
	{
	    $form.find("[name='"+fields[key]+"']").addClass(Art_Ajax.classInvalid); 
	}
    },
    
    /**
     *	Remove "invalid-class" tag from all form fields
     */
    resetFieldsInvalid : function($form)
    {
	$form.find("."+Art_Ajax.classInvalid).each(function()
	{
	    $(this).removeClass(Art_Ajax.classInvalid);
	});
    },

    
    
    /**
     *	Assign one HTML element to AJAX
     *	
     *	data-params - JSON
     *	    "action" : what to call
     *	    "update" : {
     *		"only_content" : true / false
     *		"element" : element selector
     *		"from" : variable name
     *		}
     *	    }
     *	
     *	@param object element
     *	@param string event
     *	@return bool False if not found or no data-action
     */
    bindElementToAjax : function(element, event) 
    {
	var $element = $(element);
	
	//If element is not found found
	if(!$element.length)
	{
	    return null;
	}
	
	//Decode params
	var paramsJSON = $element.attr('data-params');
	paramsJSON = paramsJSON.replace(/\'/gi,'\"');
	var params = JSON.parse(paramsJSON);
	
	var callAjax = function(ev)
	{
	    if( typeof ev !== "undefined" )
	    {
		ev.preventDefault();
	    }
	    
	    //Show confirm window if set
	    if(typeof params.confirm !== "undefined" && params.confirm.length)
	    {
		if(!confirm(params.confirm))
		{
		    return false;
		}
	    }
	 
	    //Update all CKEditor textareas
	    CKupdate();
	 
	    //If form - serialize data
	    if( $element.is('form') )
	    {
		var $form = $element;
		var form_data = $form.serialize();
	    }
	    else if( element.form )
	    {
		var $form = $(element.form);
		var form_data = $form.serialize();
	    }
	    else
	    {
		var $form = null;
		var form_data = null;
	    }
	    
	    //Disable all inputs
	    if( $form !== null )
	    {
		var $disabled = $form.find('input:enabled, select:enabled, textarea:enabled');
		$disabled.prop('disabled',true);
	    }
	    else
	    {
		var $disabled = $element;
		$element.prop('disabled',true);
	    }
	    
	    //Call AJAX
	    Art_Ajax.call(
	    {
		name: params.name,
		url: params.action,
		data: form_data,
		success: function(data) 
		{
		    console.log(data);
		    
		    //If status OK
		    if(data.status === Art_Ajax.status.ok)
		    {
			//Reenable disabled inputs
			$disabled.prop('disabled',false);
			
			//If data-update is set
			if(typeof params.update === "object")
			{
			    for(var i in params.update)
			    {
				var updatedElement = params.update[i].element;
				var from_var = params.update[i].from;			 
				var is_animated = params.update[i].animate;
				
				//If variable with HTML code is set
				if(typeof data.variables[from_var] !== "undefined")
				{
				    //Get updated element
				    var $updatedElement = $(updatedElement);
				    
				    //Update only content of element
				    if( typeof params.update.only_content !== "undefined" && params.update.only_content === true )
				    {
					if( !is_animated )
					{
					    $updatedElement.html(data.variables[from_var]);
					}
					else
					{
					    $updatedElement.stop().animate({'opacity':0},Art_Ajax.animation_time,function(){
						$updatedElement.html(data.variables[from_var]).animate({'opacity':1});
					    });
					}
				    }
				    //Replace element
				    else
				    {
					if( !is_animated )
					{
					    $updatedElement.replaceWith(data.variables[from_var]);
					}
					else
					{
					    $updatedElement.stop().animate({'opacity':0},Art_Ajax.animation_time,function(){
						$(this).replaceWith(data.variables[from_var]);
						$(updatedElement).css({'opacity':0}).animate({'opacity':1},Art_Ajax.animation_time);
					    });
					}
				    }
				}			
			    }
			    
			    //Rebind all elements to ajax
			    Art_Ajax.rebindAllElements();
			}

			//If element is form or part of form
			if( null !== $form )
			{
			    Art_Ajax.resetFieldsInvalid($form);
			}
			
			
			//If redirect is set
			if( typeof params.redirect !== "undefined")
			{
			    //If empty - refresh
			    if( params.redirect === "." )
			    {
				window.location.href = window.location.href;
			    }
			    //If set - redirect
			    else
			    {
				window.location.href = params.redirect;
			    }
			}
			//If not redirecting - show alerts
			else
			{
			    Art_AlertBox.show(data.messages);
			}
			
			//Run data-success callback function
			if( typeof $element.attr('data-success') === "function" )
			{
			    var callback = $element.attr('data-success');
			    callback();
			}
		    }
		    //If status ALERT
		    else if(data.status === Art_Ajax.status.alert)
		    {
			//Reenable disabled inputs
			$disabled.prop('disabled',false);
			
			Art_AlertBox.show(data.messages, Art_Ajax.status.alert);
			if( null !== $form )
			{
			    Art_Ajax.resetFieldsInvalid($form);
			    Art_Ajax.makeFieldsInvalid($form,data.fields);
			}
			
			//Run data-alert callback function
			if( typeof $element.attr('data-alert') === "function" )
			{
			    var callback = $element.attr('data-alert');
			    callback();
			}
		    }
		    //IF status ERROR
		    else if(data.status === Art_Ajax.status.error)
		    {			
			Art_AlertBox.show(data.messages, Art_Ajax.status.error);
			
			//Run data-alert callback function
			if( typeof $element.attr('data-alert') === "function" )
			{
			    var callback = $element.attr('data-error');
			    callback();
			}
		    }
		}
	    });
	};	
	
	//Add event listener
	$element.unbind(event,callAjax).bind(event,callAjax);
	
	//If auto submit
	if( typeof params.auto_submit !== "undefined" && params.auto_submit && typeof params.auto_submit_time !== "undefined" )
	{
	    var timeout;
	    var auto_submit = function()
	    {
		clearTimeout(timeout);
		timeout = setTimeout(function(){
		    callAjax();
		},params.auto_submit_time*1000);
	    };
	    
	    //params.auto_submit_time
	    $element.find('input,select,textarea').unbind('change',auto_submit).bind('change',auto_submit);
	}
	
	//If element has async file input and is form or is part of form
	if( params.file_async )
	{
	    //If element is form
	    if( $element.is('form') )
	    {
		//Get all uploaders
		var $uploaders = $element.find('.file_uploader');

		//Convert extensions to array
		switch( typeof params.file_extensions )
		{
		    case "string":
			var extensions = params.file_extensions.split(',');
			break;
		    case "object":
		    case "array":
			var extensions = params.file_extensions;
			break;
		    default:
			var extensions = [];
		}

		//Bind events to all file inputs
		$uploaders.each(function(){
		    $(this).Art_Uploader(params.action,params.file_request_name,extensions,params.file_max_size_single,params.file_max_size_sum);
		});
	    }
	}
	
	return element;
    },
    

    /**
     *	Bind default HTML elements to AJAX
     * 
     *	@returns array Bound elements
     */
    bindDefaultElements : function()
    {
	var elements = {'input[type=button]':'click',
			'a':'click',
			'button':'click',
			'form':'submit',
			'input[type=text]':'change'};
		    
	var result = {'click':[],'submit':[],'change':[]};
	
	for(var selector in elements)
	{
	    result[elements[selector]].push(Art_Ajax.bindBySelector(selector,elements[selector]));
	}
	
	return result;
    },
    
    /**
     *	Bind one HTML element (or set of elements) to AJAX
     *	Element must have data-method="ajax"
     *	
     *	@param {string} selector
     *	@param {string} event
     *	@returns array Bound elements
     */
    bindBySelector : function(selector, event)
    {
	var result = [];
	
	if( typeof event === "undefined" || event.length === 0 )
	{
	    event = Art_Ajax.defaultEvent;
	}
	
	Art_Ajax.elements[selector] = event;
	
	$(selector+'[data-method="ajax"]').not('.'+Art_Ajax.boundToAjaxClassName).each(function() 
	{
	   if(Art_Ajax.bindElementToAjax(this, event) !== null)
	   {
	       this.classList.add(Art_Ajax.boundToAjaxClassName);
	       result.push(this);
	   }
	});

	return result;
    },
    
    /**
     *	Rebinds all elements by selectors
     * 
     *	@return array Reboud elements
     */
    rebindAllElements : function() 
    {
	var elements = Art_Ajax.elements;
	Art_Ajax.elements = {};
	
	var result = [];
	
	for(var selector in elements)
	{
	    result.push(Art_Ajax.bindBySelector(selector,elements[selector]));
	}
	
	return result;
    },
    
    
    loadBarInit : function()
    {
	if( null === Art_Ajax.$loadingBar )
	{
	    Art_Ajax.$loadingBar = $('<div class="art_ajax_loading_bar"></div>');
	    Art_Ajax.$loadingBar.appendTo('body');
	    Art_Ajax.loadBarHide();
	}
    },
    
    
    loadBarHide : function( instant )
    {
	if( typeof instant !== "undefined" && instant )
	{
	    Art_Ajax.$loadingBar.stop().css({display:'none',opacity:0});
	}
	else
	{
	    Art_Ajax.$loadingBar.stop().animate({opacity:0}, function(){
		Art_Ajax.$loadingBar.css({display: 'none'});
	    });
	}
    },
    
    
    loadBarShow : function( instant )
    {
	if( typeof instant !== "undefined" && instant )
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block',opacity:1});
	}
	else
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block'}).animate({opacity:1});
	}
    },
    
    
    loadingBarDone : function( instant )
    {
	Art_Ajax.$loadingBar.removeClass('art_ajax_loading_bar_progress art_ajax_loading_bar_stop').addClass('art_ajax_loading_bar_done');
	
	if( typeof instant !== "undefined" && instant )
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block',opacity:1, width:'100vw'});
	}
	else
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block'}).animate({opacity:1, width:'100vw'});
	}
    },
    
    
    loadingBarProgress : function( status, instant )
    {
	Art_Ajax.$loadingBar.removeClass('art_ajax_loading_bar_done art_ajax_loading_bar_stop').addClass('art_ajax_loading_bar_progress');
	
	if( typeof instant !== "undefined" && instant )
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block',opacity:1,width:status+'vw'});
	}
	else
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block'}).animate({opacity:1,width:status+'vw'});
	}
    },
    
    loadingBarStop : function( instant )
    {
	Art_Ajax.$loadingBar.removeClass('art_ajax_loading_bar_done art_ajax_loading_bar_progress').addClass('art_ajax_loading_bar_stop');
	
	if( typeof instant !== "undefined" && instant )
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block',width:'100vw',opacity:1});
	}
	else
	{
	    Art_Ajax.$loadingBar.stop().css({display:'block'}).animate({opacity:1,width:'100vw'});
	}
    }
};


$.fn.extend({
    Art_Uploader: function(action,file_request_name,allowed_extensions,max_size_single,max_size_sum) {
	var $this = $(this);
	var name = $this.attr('data-name');
	var $input = $('<input type="file" name="'+name+'" multiple>');
	var $responses = $('<div class="file_uploader_responses"></div>');
	var size_sum = 0;
	
	//Add placeholder to uploader
	if( typeof $this.attr('data-placeholder') !== "undefined" )
	{
	    $this.append('<div class="file_uploader_placeholder">'+$this.attr('data-placeholder')+'</div>');
	}

	//Add input to uploader
	$this.append($input);
	$this.append($responses);
	$input.wrap('<div class="file_uploader_input">');
	
	var inputChanged = function() {
	    var length = this.files.length;
	    //Add new files
	    for( var i = 0; i < length; i++ )
	    {
		//Add file to list
		//Validate the upload
		var pre_extension = this.files[i].name.split('.');
		var extension = pre_extension.pop();
		var size = this.files[i].size/1048576;
		var error = [];

		if( typeof allowed_extensions === "object" && allowed_extensions.length > 0 && allowed_extensions[0] !== '*' && allowed_extensions.indexOf(extension) === -1 )
		{
		    error.push(1);
		}
		
		if( typeof max_size_single !== "undefined" && size > max_size_single )
		{
		    error.push(2);
		}
		
		if( typeof max_size_sum !== "undefined" && ( size_sum + size ) > max_size_sum  )
		{
		    error.push(3);
		}
		
		//If valid - upload
		if( error.length === 0 )
		{    
		    Art_Upload_File(this.files[i],name,action,file_request_name,$responses);
		}
		else
		{
		    if( error.indexOf(1) !== -1 )
		    {
			if( extension.length > 0 )
			{
			    var name = pre_extension.join('.')+'<span class="file_uploader_error_item">.'+extension+'</span>';
			}
			else
			{
			    var name = '<span class="file_uploader_error_item">'+this.files[i].name+'</span>';

			}
		    }
		    else
		    {
			var name = this.files[i].name;
		    }
		    
		    if( error.indexOf(2) !== -1 )
		    {
			var size = '<span class="file_uploader_error_item">'+Math.round((this.files[i].size/1048576)*100)/100+' MB</span>';
		    }
		    else
		    {
			var size = (Math.round((this.files[i].size/1048576)*100)/100) + 'MB';
		    }
		    
		    $responses.prepend('<div class="file_uploader_item file_uploader_error">\
					<div class="file_uploader_item_top_row ">\n\
					    <div class="file_uploader_item_name">'+name+'</div>\n\
					</div>\n\
					<div class="file_uploader_item_size">'+size+'</div>\n\
					<div class="file_uploader_item_status_bar" style="width:100%;"></div>\n\
					<div class="file_uploader_item_status_icon">'+Art_Ajax.statusIconError+'</div>\n\
					</div>');
		}
	    }
	    //Clear input
	    $this.wrap('<form>').closest('form').get(0).reset();
	    $this.unwrap();
	};

	//When files are added
	$input.unbind('change',inputChanged).bind('change',inputChanged);
	
	$input.on('dragover',function(){
	    $this.addClass('file_uploader_dragover');
	});
	$input.on('dragleave drop',function(){
	    $this.removeClass('file_uploader_dragover');
	});	
    }
});

var Art_Upload_File = function(file,name,action,file_request_name,$responseWindow) {
    
    var fileGUID = rand_str(32);
    var $statusBar = $('<div class="file_uploader_item_status_bar" style="width:0.01%;"></div>');
    var $statusCount = $('<div class="file_uploader_item_status_count">0%</div>');
    var $statusIcon = $('<div class="file_uploader_item_status_icon">'+Art_Ajax.statusIconLoading+'</div>');
    var $abortIcon = $('<div class="file_uploader_item_abort" data-id="'+fileGUID+'">'+Art_Ajax.stopIcon+'</div>');
    var $topRow = $('<div class="file_uploader_item_top_row"></div>');
    var $response = $('<div class="file_uploader_item"></div>');

    $topRow.append('<div class="file_uploader_item_name">'+file.name+'</div>');
    $topRow.append($abortIcon);
    $response.append($topRow);
    $response.append($statusBar);
    $response.append('<div class="file_uploader_item_size">'+Math.round((file.size/1048576)*100)/100+' MB</div>');
    $response.append($statusIcon);
    $response.append($statusCount);

    $responseWindow.prepend($response);

    var form_data = new FormData();

    //Append a file to data
    form_data.append( name, file );
    form_data.append('guid',fileGUID);

    //Send request via AJAX
    var $request = Art_Ajax.call(
    {
	name: file_request_name,
	url: action,
	data: form_data,
	processData: false,
	contentType: false,
	progress: function(percent) {
	    $statusCount.html(Math.round(percent)+'%');
	    $statusBar.stop().animate({width:percent+'%'},500);
	},
	success: function(data)
	{
	    $statusIcon.html(Art_Ajax.statusIconLoaded);
	    $abortIcon.fadeOut(500);
	    console.log(data);
	},
	error: function(er)
	{
	    $statusIcon.html(Art_Ajax.statusIconError);
	    $abortIcon.fadeOut(500);
	    console.log(er.responseText);
	}
    });	
    
    $abortIcon.click(function(){
	$request.abort();
	$response.addClass('file_uploader_stopped');
	$statusCount.html('');
	$abortIcon.fadeOut(500);
    });
};


/**
 *  Simple image fadeshow
 */
var Art_Fadeshow = {
    switch_time: 5000,
    fade_time: 500,
    run : function( selector, switch_time, fade_time ) {
	if( typeof switch_time === "undefined" )
	{
	    switch_time = Art_Fadeshow.switch_time;
	}
	if( typeof fade_time === "undefined" )
	{
	    fade_time = Art_Fadeshow.fade_time;
	}

	var $fadeshow = $(selector);
	var $items = $fadeshow.children();

	$fadeshow.css({'position':'relative','height':'100%'});
	$items.css({'position':'absolute','opacity':0,'display':'block','height':'auto','max-width':'100%'});
	$items.last().css({'position':'relative'});
	$items.first().css({'opacity':1});
	
	var count = $fadeshow.children().length;
	var current = 1;
	var interval = setInterval(function(){
	    var next = current + 1;
	    if( next > count )
	    {
		next = 1;
	    }
	    $fadeshow.find(':nth-child('+current+')').animate({'opacity':0},fade_time,'swing');
	    $fadeshow.find(':nth-child('+next+')').animate({'opacity':1},fade_time,'swing');
	    current = next;
	},switch_time);
    }
};




$(function(){
    Art_Ajax.loadBarInit();
    Art_Ajax.bindDefaultElements();
});