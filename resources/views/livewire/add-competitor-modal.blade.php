<div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-2xl w-full p-6 relative flex flex-col md:flex-row gap-6" @click.away="showModal = false">
  
  <!-- Form elements (Left column) -->
  <form wire:submit.prevent="save" class="space-y-4 flex-1">
    <h3 class="text-xl font-extrabold text-gray-900">Add a Competitor</h3>
    
    <div>
      <label for="add-comp-name" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Competitor Name</label>
      <input wire:model="name" id="add-comp-name" type="text" placeholder="Stripe" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
      @error('name') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
      <label for="add-comp-url" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Website URL</label>
      <input wire:model="websiteUrl" id="add-comp-url" type="url" placeholder="https://stripe.com" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
      @error('websiteUrl') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
      <label for="add-comp-pricing" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pricing Page URL</label>
      <input wire:model="pricingUrl" id="add-comp-pricing" type="url" placeholder="https://stripe.com/pricing" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
      @error('pricingUrl') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label for="add-comp-freq" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Scrape Frequency</label>
        <select wire:model="frequency" id="add-comp-freq" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
          <option value="hourly">Every hour</option>
          <option value="every_6_hours">Every 6 hours</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
        </select>
        @error('frequency') <span class="text-xs text-red-600 block mt-1">{{ $message }}</span> @enderror
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Alert Channels</label>
        <div class="space-y-1.5 text-xs text-gray-600">
          <label class="flex items-center gap-1.5">
            <input type="checkbox" checked disabled class="rounded border-gray-300 text-indigo-600 h-3.5 w-3.5">
            <span>Email</span>
          </label>
          <label class="flex items-center gap-1.5">
            <input type="checkbox" checked disabled class="rounded border-gray-300 text-indigo-600 h-3.5 w-3.5">
            <span>Slack</span>
          </label>
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-2 pt-3">
      <button type="button" @click="showModal = false" class="text-xs font-semibold px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700">Cancel</button>
      <button type="submit" class="text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Start Monitoring</button>
    </div>
  </form>

  <!-- Details side panel preview (Right column) -->
  <div class="hidden md:block w-52 bg-slate-50 border border-gray-200/60 p-4 rounded-xl flex flex-col justify-between text-xs">
    <div>
      <h4 class="font-bold text-gray-950 mb-3">What happens next?</h4>
      <ul class="space-y-4 relative text-gray-500">
        <li class="flex items-start gap-2 relative">
          <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full mt-1.5 shrink-0"></span>
          <div>
            <span class="font-bold text-gray-900 block">Crawler Fetch</span>
            PriceWatch fetches the public HTML contents of your competitor's page safely.
          </div>
        </li>
        <li class="flex items-start gap-2">
          <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full mt-1.5 shrink-0"></span>
          <div>
            <span class="font-bold text-gray-900 block">Pricing Extracted</span>
            Extracted HTML text is parsed, identifying plan prices, tiers, and limits.
          </div>
        </li>
        <li class="flex items-start gap-2">
          <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full mt-1.5 shrink-0"></span>
          <div>
            <span class="font-bold text-gray-900 block">Baseline Saved</span>
            A benchmark configuration log is saved, ready to track future updates.
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
