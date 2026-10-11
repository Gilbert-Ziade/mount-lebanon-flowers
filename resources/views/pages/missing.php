<h1>Can't find your flower?</h1>
<p style="color:var(--mute);max-width:60ch;margin:8px 0">Send us a photo on WhatsApp together with the exact place where you saw it. We will identify it and add it to the catalog.</p>
<form class="box" method="post" action="<?= e(url('/missing/whatsapp')) ?>" target="_blank" rel="noopener">
  <div class="form-grid">
    <label>Your name<input name="name" placeholder="Optional" maxlength="80"></label>
    <label>Region
      <select name="region">
        <?php foreach ($regions as $r): ?><option><?= e($r) ?></option><?php endforeach; ?>
        <option>Other</option>
      </select>
    </label>
    <label class="full">Exact location<input name="location" placeholder="Village, road, landmark or Google Maps link" maxlength="300"></label>
    <label class="full">Notes<textarea name="notes" rows="3" placeholder="Color, height, what it grows next to..." maxlength="600"></textarea></label>
  </div>
  <p class="sci" style="margin:12px 0">WhatsApp cannot attach photos from a link, so you will add the picture in the chat.</p>
  <button class="btn wa" type="submit">Open WhatsApp</button>
</form>
