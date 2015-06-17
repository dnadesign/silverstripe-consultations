<% include Header %>

<div class="banner_intro">
	<div class="wrapper">
		<h1>$Title</h1>
		<% if not Idea %>
		<p class="intro">$Introduction</p>
		<% end_if %>
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

<% if LatestComments %>
<div class="wrapper graphs">
	<div class="wrapper_grey">
		<div class="stat_up stat_up__four stat_up__dark stat_up__tight">
			<% if Idea %>
				<% with Idea %>
					<% include Idea_Stats %>
				<% end_with %>
			<% else %>
				<% include OverallStats %>
			<% end_if %>
		</div>

		<% if Idea %>
			<div class="summary_graphs halves summary_graphs__dark">
				<div class="halve_left">
					<% include Graph_Benefit %>
				</div>

				<div class="halve_right">
					<% include Graph_Priority %>
				</div>
			</div>
		<% else %>
			<div class="summary_graphs summary_graphs__dark">
				<div class="summary_graph_single">
					<% include Graph_Benefit %>
				</div>
			</div>
		<% end_if %>
	</div>
</div>
<% else %>
	<div class="search_content single_comment">
		<div class="wrapper">
			<div class="col_left col_left__wide">
				<ul class="search_results">
					<li class="search_result">
						<div class="search_result_inner">
							<h3>No feedback received on this idea yet.</h3> 
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
<% end_if %>

<div class="wrapper">
	<% if LatestPromotedComments %>
	<div class="comments_grid__container">
		<div class="comments_grid comments_grid__left">
			<ul>
				<% loop LatestPromotedComments %>	
					<% if Odd %>
						<li><% include Comment %></li>
					<% end_if %>
				<% end_loop %>
			</ul>
		</div>

		<div class="comments_grid comments_grid__right">
			<ul>
				<% loop LatestPromotedComments %>	
					<% if Even %>
						<li><% include Comment %></li>
					<% end_if %>
				<% end_loop %>
			</ul>
		</div>
	</div>
	<% end_if %>

	<% if LatestComments %>
	<div class="halves">
		<div class="halve_left">
			<p class="button_right"><a href="$Link(comments)" class="button button__dark button__small">See all comments</a></p>
		</div>

		<div class="halve_right">
			<p class="button_left"><a href="$Link(map)" class="button  button__yellow button__small">View feedback on a map</a></p>
		</div>
	</div>
	<% end_if %>
</div>