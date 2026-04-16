Recommended upgrade plan for 3neti/instruction

Goal

Promote LBHurtado\Instruction\Models\InstructionItem from a plain pricing model into a wallet-enabled instruction revenue model, so x-change can safely do:
	•	pending revenue snapshots
	•	revenue collection
	•	per-instruction-item wallet reporting
	•	future dashboards and price history

⸻

Phase 1 — Make InstructionItem wallet-capable

1. Update the model

Bring the package model closer to your legacy host-app version.

Target shape:
	•	implement ProductInterface
	•	use HasWallet
	•	use HasWalletFloat
	•	keep revenueDestination()
	•	add revenueCollections()
	•	restore helper methods:
	•	getAmountProduct()
	•	getMetaProduct()
	•	getUniqueId()
	•	attributesFromIndex()
	•	getCategoryAttribute()

Target interface/traits
use Bavix\Wallet\Interfaces\ProductInterface;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWalletFloat;

class InstructionItem extends Model implements ProductInterface
{
    use HasFactory;
    use HasWallet;
    use HasWalletFloat;
}

Why this is correct

This matches your old accounting model exactly:
	•	revenue lands in the instruction item’s wallet
	•	later collection moves money out of that wallet

⸻

Phase 2 — Add any missing schema fields

Your current package migration may already include some of these, but the target schema should support:
	•	name
	•	index unique
	•	type
	•	price integer minor units
	•	currency
	•	meta json nullable
	•	revenue_destination_type nullable
	•	revenue_destination_id nullable
	•	timestamps

If the package migration already has destination fields, keep them.
If not, add a new migration rather than rewriting the original migration if the package has already been used.

Add migration if needed

Add a package migration like:
	•	add_revenue_destination_to_instruction_items_table

with:
$table->nullableMorphs('revenue_destination');

if those columns are not already there.

⸻

Phase 3 — Add price history support

Your legacy app had InstructionItemPriceHistory, which is genuinely useful.

Add model
	•	LBHurtado\Instruction\Models\InstructionItemPriceHistory

Add migration
	•	instruction_item_price_history

Fields:
	•	instruction_item_id
	•	old_price
	•	new_price
	•	currency
	•	changed_by nullable
	•	reason nullable
	•	effective_at
	•	timestamps

Add relationship on InstructionItem
public function priceHistory()
{
    return $this->hasMany(InstructionItemPriceHistory::class);
}

This is not required for x-change revenue to work, but it is a good package-level upgrade and matches your original design.

⸻

Phase 4 — Preserve pricing semantics

Your package should explicitly define that:
	•	price is stored in minor units
	•	display formatting happens outside or via helpers
	•	getAmountProduct() returns minor units

That matches both your old host app and your current x-change pricing work.

Keep this behavior
public function getAmountProduct(Customer $customer): int|string
{
    return $this->price;
}

You can later add special logic again, like system-user exemptions, but the package should first restore the core behavior.

⸻

Phase 5 — Restore revenue relationships

On InstructionItem:
public function revenueDestination()
{
    return $this->morphTo('revenue_destination');
}

public function revenueCollections()
{
    return $this->hasMany(\LBHurtado\Instruction\Models\RevenueCollection::class);
}

Even if RevenueCollection ultimately lives in x-change instead of instruction, the direction should be decided now.

My recommendation

Keep RevenueCollection in x-change, not instruction.

Why:
	•	InstructionItem belongs to the pricing/instruction domain
	•	actual collection is orchestration/financial workflow
	•	x-change is the orchestration package

So for now:
	•	keep revenueDestination() in instruction
	•	keep revenueCollections() optional, or omit it from instruction if the model lives in x-change

⸻

Phase 6 — Package dependency changes

If InstructionItem becomes walletable, then 3neti/instruction must explicitly depend on the wallet package.

Add package requirements

In 3neti/instruction/composer.json, add the relevant Bavix wallet package dependency you are already using in the monorepo/app.
"bavix/laravel-wallet": "^11.0 || ^12.0"

Use the exact version range compatible with your ecosystem.

Also ensure Laravel version constraints align with your current packages.

⸻

Phase 7 — Add tests in 3neti/instruction

Minimum new tests:

Model tests
	•	InstructionItem implements ProductInterface
	•	InstructionItem has wallet relation
	•	InstructionItem wallet balance starts at zero
	•	InstructionItem can receive funds
	•	InstructionItem can transfer funds

Pricing helper tests
	•	getAmountProduct() returns stored price
	•	getMetaProduct() returns expected title/description
	•	attributesFromIndex() builds sane defaults
	•	category accessor works

Schema tests
	•	revenue_destination_type and revenue_destination_id exist if expected
	•	price history migration works

These tests matter because once instruction becomes walletable, x-change will depend on it heavily.

⸻

