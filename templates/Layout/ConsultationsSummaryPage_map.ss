<% include Header %>

<div class="banner_intro">
	<div class="wrapper">
		<h1>$Title</h1>

		<p class="intro">$Introduction</p>

		$IdeaSelectorForm

		<% include ShareThis %>
	</div>
</div>

<% if Idea && Idea.Tagline %>
	<div class="wrapper">
		<div class="feature_text feature_tagline">
			$Idea.Tagline
		</div>
	</div>
<% end_if %>

<div class="search_content map_content">
	<div class="wrapper">
		<div class="col_left col_left__wide col__tight">
			<ul class="key key__inline">
				<li><span class="icon icon-color" style="background-color: #20202F"></span> Extremely unhappy</li>
				<li><span class="icon icon-color" style="background-color: #757584"></span> Moderately unhappy</li>
				<li><span class="icon icon-color" style="background-color: #EAE3BE"></span> Neither happy nor unhappy</li>
				<li><span class="icon icon-color" style="background-color: #FFEC66"></span> Moderately happy</li>
				<li><span class="icon icon-color" style="background-color: #FFD500"></span> Extremely happy</li>
			</ul>
		</div>
		<div class="col_right col_right__wide col__tightbottom">
			<div class="search_view_options">
				<ul>
					<li class="search_view_options_view first">
						<a href="$Link(comments)" class="list_icon"><span class="search_view_options_text">List</span></a>
					</li>
					
					<li class="search_view_options_view active last">
						<a href="$Link(map)" class="map_icon"><span class="search_view_options_text">Map</span></a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="wrapper">
		<div class="map_holder_outside">
			<div class="map_holder" data-kml="$Link('kmlfile')">

			</div>

			<div class="map_loading"><p>Data Loading..</p></div>

			<noscript>
				<p>Please enable javascript to view map</p>
			</noscript>
		</div>

		<p class="credit"><small>$Submissions.Count submissions across all suburbs. KML location data licensed under <a href="https://koordinates.com/license/attribution-3-0-new-zealand/">Creative Commons Attribution 3.0 New Zealand.</a></small></p>

		<table class="map_data_set">
			<thead>
				<th>Suburb</th>
				<th>Submissions</th>
				<th>Positive</th>
				<th>Negative</th>
			</thead>
			
			<tbody>
				<% loop KmlDataSet %>
					<tr id="wellington-city-suburbsId-$Key" <% if TotalSubmissions %>data-marker="<div class='marker_inner'><h2><a href='$Link'>$Title</a></h2><p class='marker_positive'><strong>$Percentage<sup>%</sup></strong>positive feedback</p><p class='marker_comments'><strong>$TotalSubmissions</strong>Submissions</p></div>"<% end_if %>>
						<td>$Title</td>
						<td>$TotalSubmissions</td>
						<td>$PositiveSubmissions</td>
						<td>$NegativeSubmissions</td>
					</tr>
				<% end_loop %>
			</tbody>
		</table>
	</div>
</div>