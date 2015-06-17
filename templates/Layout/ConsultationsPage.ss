<% include Header %>


<div class="banner_intro">
	<div class="wrapper">
		<h1>$Title</h1>

		<p class="intro">$Introduction</p>
	</div>
</div>

<div class="wrapper_grey">
	<div class="wrapper spaced">
		<% loop Children %>
			<div class="big_idea big_idea_{$Modulus(3)}">
				<div class="big_idea__title <% if CategoryImage %>has_big_idea_image<% end_if %>">
					<% if CategoryImage %>
						<div class="big_idea__image">
							$CategoryImage
						</div>
					<% end_if %>

					<% cached 'engagementcircle', $ID, List(TypeformSubmission).max(LastEdited), List(TypeformSubmission).count()  %>
					<% include EngagementCircle Offset=$EngagementOffset.Offset, Rotate=$EngagementOffset.Rotate %>
					<% end_cached %>
					
					<div class="big_idea__icon">
						$SVGIcon.RAW
					</div>
				</div>

				<div class="big_idea__content">
					<h2>$Title</h2>
					<p>$Introduction</p>

					<div class="big_idea_children">

						<% cached 'bigideaslanding', $ID, List(SiteTree).max(LastEdited), List(SiteTree).count() %>
						<% if Children %>
							<% loop Children %>
								<li><a href="$Link">$MenuTitle</a></li>
							<% end_loop %>
						<% else %>
							<p><a href="$Link">View</a></p>
						<% end_if %>
						<% end_cached %>
					</div>
				</div>
			</div>
		<% end_loop %>
	</div>
</div>