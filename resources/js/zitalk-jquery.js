var zitalk = 
{
	max: 0,
	deleteSession: function()
	{
		$.ajax(
		{
			async: false,
			type: 'POST',
			url: "?"+Math.random()+"="+Math.random(),
			data:
			{
				action: 'deleteSession',
			}
		});			
	},
	login: function()
	{
		$('.login button').on('click', function(e)
		{
			e.preventDefault();
			var data = $('.login input').val();
			$.ajax(
			{
				async: false,
				type: 'POST',
				url: "?"+Math.random()+"="+Math.random(),
				data:
				{
					action: 'login',
					data: data
				},
				success: function(res)
				{
					if(res=='true')
					{
						$('.login').hide();
						$('.message').show();
						$('.message span').first().text("Welcome "+data+"!");
						$('.message input').focus();
					}
					else
						window.alert('USER ONLINE');
				}
			});
		});
	},
	logout: function()
	{
		$('.message .logout').on('click', function(e)
		{
			e.preventDefault();	
			var input = $('.message input');
			$.ajax(
			{
				async: false,
				type: 'POST',
				url: "?"+Math.random()+"="+Math.random(),
				data:
				{
					action: 'logout',
				},
				success: function()
				{
					$('.message').hide();
					$('.message input').val('');
					$('.login').show();
					$('.login input').focus();
				}
			});				
		});	
	},
	send: function()
	{
		$('.message .send').on('click', function(e)
		{
			e.preventDefault();	
			var input = $('.message input');
			$.ajax(
			{
				async: false,
				type: 'POST',
				url: "?"+Math.random()+"="+Math.random(),
				data:
				{
					action: 'write',
					data: input.val(),
				},
				success: function()
				{
					input.val('');
					input.focus();
				}
			});				
		});
	},
	read: function(id)
	{
		var self = this;

		if(typeof(id)=='undefined')
			id = 0;
		$.ajax(
		{
			async: false,
			type: 'POST',
			dataType: 'json',
			url: "?"+Math.random()+"="+Math.random(),
			data:
			{
				action: 'read',
				data: id
			},
			success: function(res)
			{
				var tbody = $('tbody');

				for(var i in res)
				{
					self.max = res[i]['id'];

					var tr = $('<tr></tr>');
					var td = $('<td></td>');
					var text = document.createTextNode(res[i]['name']);

					td.append(text);
					tr.append(td);

					var td = $('<td></td>');						
					var text = document.createTextNode(res[i]['comment']);					

					td.append(text);
					tr.append(td);

					tbody.prepend(tr);
				}
			}
		});
	},
	maxId: function()
	{
		var self = this;

		$.ajax(
		{
			async: false,
			type: 'POST',
			url: "?"+Math.random()+"="+Math.random(),
			data:
			{
				action: 'maxId',
				data: self.max
			},
			success: function(res)
			{
				if(res>self.max)
				{
					self.read(self.max);
					self.max = res;
				}
				window.setTimeout(function()
				{
					self.maxId();
				}, 5 * 1000);
			}
		});
	},
	readOu: function()
	{
		var self = this;

		$.ajax(
		{
			async: false,
			type: 'POST',
			url: "?"+Math.random()+"="+Math.random(),
			data:
			{
				action: 'readOu'
			},
			success: function()
			{
				window.setTimeout(function()
				{
					self.readOu();
				}, 15 * 1000);	
			}
		});	
	},
	main: function()
	{
		var self = this;

		$('tbody').empty();
		$('form').on('submit', function(e)
		{
			e.preventDefault();
		});
		this.deleteSession();
		this.login();			
		this.logout();			
		this.send();			
		this.read();

		window.setTimeout(function()
		{
			self.maxId();
		}, 5 * 1000);

		window.setTimeout(function()
		{
			self.readOu();
		}, 15 * 1000);			
	}
};
