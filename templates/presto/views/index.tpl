{{title}} {{authors|length|numberFormat}}
{% for author in authors %}
	<h2 class="position{{thisPosition}} {% if thisIsFirst %}first-in-row{% elseif thisIsLast %}last-in-row{% else %}somwhere-in-the-middle{% endif %}">{{author.name}}</h2>
	<p>Narodeny {{ author.born|date("d.m.Y @H:i") }}</p>
{% endfor %}