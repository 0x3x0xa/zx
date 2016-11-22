function create_address_select(e, g, n, p, q, r) {
	function h() {
		var b, f;
		this.clear_options();
		"province" == this.addr && (b = address_data[0].Province, a.clear_options(), c.clear_options());
		"capital" == this.addr && (b = s(), c.clear_options());
		if ("city" == this.addr) a: {
			capitals = s();
			capital_name = a.options[a.selectedIndex].text;
			for (b = 0; b < capitals.length; b++) if (capitals[b].Name == capital_name) {
				b = capitals[b].City;
				break a
			}
			b = null
		}
		for (f = 0; f < b.length; f++) this.add_option(b[f].Name, b[f].Value)
	}
	function s() {
		provinces = address_data[0].Province;
		province_name = d.options[d.selectedIndex].text;
		for (var b = 0; b < provinces.length; b++) if (provinces[b].Name == province_name) return provinces[b].Capital;
		return null
	}
	function k(b, f) {
		var a = document.createElement("OPTION");
		a.text = b;
		a.value = "value" == n || "" == f ? f : b;
		this.options.add(a)
	}
	function l() {
		this.options.length = 1
	}
	function m(b) {
		for (var a = 0; a < this.options.length; a++) if (b == ("value" == n ? this.options[a].value : this.options[a].text)) {
			this.selectedIndex = a;
			this.onchange();
			break
		}
	}
	var t = document.getElementById(g);
	if ("object" == typeof g) var d = g.province,
		a = g.capital,
		c = g.city;
	else t.innerHTML = "<select    name='" + e + "Province' id='" + e + "Province' class='select province' style='margin-bottom:5px;height:31px;line-height:31px;width:33%' ></select><select name='" + e + "Capital' id='" + e + "Capital' class='select city' style='margin-bottom:5px;height:31px;line-height:31px;width:33%'  ></select><select  name='" + e + "City' id='" + e + "City' class='select area' style='margin-bottom:5px;height:31px;line-height:31px;width:33%' ></select>", d = document.getElementById(e + "Province"), a = document.getElementById(e + "Capital"), c = document.getElementById(e + "City");
	d.add_option = k;
	d.clear_options = l;
	d.addr = "province";
	d.bind_data = h;
	d.onchange = function() {
		a.bind_data()
	};
	d.add_option("\u9009\u62e9\u7701\u4efd...", "");
	d.set_default = m;
	a.add_option = k;
	a.clear_options = l;
	a.addr = "capital";
	a.bind_data = h;
	a.onchange = function() {
		c.bind_data()
	};
	a.add_option("\u9009\u62e9\u57ce\u5e02...", "");
	a.set_default = m;
	c.add_option = k;
	c.clear_options = l;
	c.addr = "city";
	c.bind_data = h;
	c.onchange = function() {};
	c.add_option("\u9009\u62e9\u5730\u533a...", "");
	c.set_default = m;
	d.bind_data();
	p && d.set_default(p);
	q && a.set_default(q);
	r && c.set_default(r)
};