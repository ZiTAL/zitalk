var zitalk = 
{
	max: 0,
	show: function(element)
	{
		console.log('show');
		element.style.display = 'block';
	},
	hide: function(element)
	{
		console.log('hide');
		element.style.display = 'none';
	},
	deleteSession: function()
	{
		console.log('deleteSession');
		var ajax = new zajax();
		//ajax.async = false;
		ajax.page = "?"+Math.random()+"="+Math.random();
		ajax.query = "action=deleteSession";
		ajax.request();
		return false;
	},
	login: function()
	{
		console.log('login');
		var self = this;

		var container = document.getElementsByClassName('login')[0];
		var button = container.getElementsByTagName('button')[0];
		button.onclick = function()
		{
			var data = container.getElementsByTagName('input')[0].value;

			var ajax = new zajax();
			//ajax.async = false;
			ajax.page = "?"+Math.random()+"="+Math.random();
			ajax.query = "action=login&data="+encodeURIComponent(data);
			ajax.onComplete = function(res)
			{
				if(res=='true')
				{
					self.hide(container);
					var message = document.getElementsByClassName('message')[0];
					self.show(message);

					var span = message.getElementsByTagName('span')[0];
					var text = document.createTextNode("Welcome "+data+"!");
					span.appendChild(text);
					var input = message.getElementsByTagName('input')[0];
					input.focus();
				}
				else
					window.alert('USER ONLINE');
			};
			ajax.request();						
		};
	},
	logout: function()
	{
		console.log('logout');
		var self = this;

		var message = document.getElementsByClassName('message')[0];		
		var button = message.getElementsByClassName('logout')[0];
		button.onclick = function()
		{
			var input = message.getElementsByTagName('input')[0];

			var ajax = new zajax();
			//ajax.async = false;
			ajax.page = "?"+Math.random()+"="+Math.random();
			ajax.query = "action=logout";
			ajax.onComplete = function()
			{
				self.hide(message);
				input.value = '';
				var login = document.getElementsByClassName('login')[0];
				var input2 = login.getElementsByTagName('input')[0];
				self.show(login);
				input2.focus();
			};
			ajax.request();			
		};
	},
	send: function()
	{
		console.log('send');
		var message = document.getElementsByClassName('message')[0];
		var button = message.getElementsByClassName('send')[0];

		button.onclick = function()
		{
			var input = message.getElementsByTagName('input')[0];			

			var ajax = new zajax();
			//ajax.async = false;
			ajax.page = "?"+Math.random()+"="+Math.random();
			ajax.query = "action=write&data="+encodeURIComponent(input.value);
			ajax.onComplete = function()
			{
				input.value = '';
				input.focus();
			};
			ajax.request();
		};
	},
	read: function(id)
	{
		console.log('read');
		var self = this;

		if(typeof(id)=='undefined')
			id = 0;

		var ajax = new zajax();
		//ajax.async = false;
		ajax.response = 'json',
		ajax.page = "?"+Math.random()+"="+Math.random();
		ajax.query = "action=read&data="+id;
		ajax.onComplete = function(res)
		{
			var tbody = document.getElementsByTagName('tbody')[0];

			for(var i in res)
			{
				self.max = res[i]['id'];

				var tr = document.createElement('tr');
				var td = document.createElement('td');
				var text = document.createTextNode(res[i]['name']);

				td.appendChild(text);
				tr.appendChild(td);

				var td = document.createElement('td');			
				var text = document.createTextNode(res[i]['comment']);

				td.appendChild(text);
				tr.appendChild(td);

				if(tbody.hasChildNodes())
					tbody.insertBefore(tr, tbody.childNodes[0]);
				else
					tbody.appendChild(tr);
			}
		};
		ajax.request();
	},
	maxId: function()
	{
		console.log('maxId');
		var self = this;

		var ajax = new zajax();
		//ajax.async = false;
		ajax.page = "?"+Math.random()+"="+Math.random();
		ajax.query = "action=maxId&data="+self.max;
		ajax.onComplete = function(res)
		{
			if(res>self.max)
			{
				self.read(self.max);
				self.max = res;
			}
			window.setTimeout(function()
			{
				self.maxId();
			}, 2 * 1000);
		};
		ajax.request();
	},
	readOu: function()
	{
		console.log('readOu');
		var self = this;

		var ajax = new zajax();
		//ajax.async = false;
		ajax.page = "?"+Math.random()+"="+Math.random();
		ajax.query = "action=readOu";
		ajax.onComplete = function()
		{
			window.setTimeout(function()
			{
				self.readOu();
			}, 5 * 1000);	
		};
		ajax.request();
	},
	main: function()
	{
		console.log('main');
		var self = this;

		var forms = document.getElementsByTagName('form');
		for(var i in forms)
		{
			forms[i].onsubmit = function()
			{
				return false;
			};
		}

		var tbody = document.getElementsByTagName('tbody')[0];
		while(tbody.hasChildNodes())
			tbody.removeChild(tbody.firstChild);

		self.deleteSession();
		self.login();			
		self.logout();			
		self.send();			
		self.read();

		window.setTimeout(function()
		{
			self.maxId();
		}, 2 * 1000);

		window.setTimeout(function()
		{
			self.readOu();
		}, 5 * 1000);
	}	
};