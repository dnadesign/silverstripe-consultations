<div class="consultations">
	<ul>
		<% loop Children %>
			<li class="consultation">
				<% if CategoryImage %>
					<div class="consultation_category__image">
						$CategoryImage
					</div>
				<% end_if %>

				<% include EngagementCircle Offset=$EngagementOffset.Offset, Rotate=$EngagementOffset.Rotate %>
					
				<div class="consultation_category__icon">
					$SVGIcon.RAW
				</div>

				<div class="consultation_category__content">
					<h2>$Title</h2>

					$Content

					<div class="consultation_category__children">
						<% if Children %>
							<% loop Children %>
								<li><a href="$Link">$MenuTitle</a></li>
							<% end_loop %>
						<% else %>
							<p><a href="$Link">View</a></p>
						<% end_if %>
					</div>
				</div>
			</li>
		<% end_loop %>
	</ul>>
</div>