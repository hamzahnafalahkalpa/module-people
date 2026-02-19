# CLAUDE.md - Module People

This file provides guidance to Claude Code when working with the `hanafalah/module-people` package.

## Overview

Module People is a Laravel package that provides person/people management functionality for the Wellmed multi-tenant healthcare management system. It handles personal information, identity documents, addresses, family relationships, and demographic reference data (religion, education, marital status, family roles).

**Package:** `hanafalah/module-people`
**Namespace:** `Hanafalah\ModulePeople`

## CRITICAL WARNING: ServiceProvider Memory Issues

The current ServiceProvider uses `registers(['*'])` combined with explicit `'Services'` registration:

```php
public function register()
{
    $this->registerMainClass(ModulePeople::class)
        ->registerCommandService(Providers\CommandServiceProvider::class)
        ->registers([
            '*',  // <-- Auto-registers all safe methods
            'Services' => function () {
                $this->binds([
                    Contracts\ModulePeople::class => new ModulePeople
                ]);
            }
        ]);
}
```

### Potential Memory Issues

1. **`registers(['*'])` auto-loads ALL classes** during boot phase
2. **Schema classes extend `PackageManagement`** which uses `HasModelConfiguration` trait
3. **`HasModelConfiguration` can trigger chain loading** via `config('database.models')` calls
4. **In Laravel Octane**, this can cause memory exhaustion (536MB limit)

### The Problem Chain

```
registers(['*'])
    |-> registerSchema()
        |-> Loads People, FamilyRelationship, etc. Schema classes
            |-> Each extends BaseModulePeople -> PackageManagement
                |-> Uses HasModelConfiguration trait
                    |-> Calls config('database.models')
                        |-> May trigger additional class loading
                            |-> POTENTIAL MEMORY EXHAUSTION
```

### Safe Modification Pattern

If you need to modify the ServiceProvider, prefer explicit registration:

```php
public function register()
{
    $this->registerMainClass(ModulePeople::class)
        ->registerCommandService(Providers\CommandServiceProvider::class);

    // Register services with closures (deferred loading)
    $this->app->singleton(Contracts\ModulePeople::class, fn() => new ModulePeople);
}
```

### Before Modifying This Module

- [ ] Understand `BaseServiceProvider->registers()` behavior in `laravel-support`
- [ ] Test in all dependent applications (backbone, hq, lite, plus)
- [ ] Monitor memory usage: `docker logs wellmed-backbone 2>&1 | grep -i "memory\|fatal"`
- [ ] Verify no circular dependency issues occur
- [ ] Clear config cache after changes: `php artisan config:clear`

## Dependencies

```json
{
    "require": {
        "hanafalah/laravel-support": "dev-main",
        "hanafalah/module-user": "dev-main",
        "hanafalah/module-card-identity": "dev-main",
        "hanafalah/module-regional": "dev-main"
    }
}
```

- **laravel-support** - Base classes, utilities, service provider, PackageManagement
- **module-user** - User management integration (UserReference relationship)
- **module-card-identity** - Identity card management (NIK, SIM, Passport, etc.)
- **module-regional** - Address and location management (KTP, Residence addresses)

## Directory Structure

```
module-people/
├── assets/
│   ├── config/
│   │   └── config.php                  # Module configuration
│   └── database/
│       └── migrations/
│           ├── 0000_00_00_000005_create_family_relationships_table.php
│           └── 0001_01_01_000006_create_peoples_table.php
├── src/
│   ├── Commands/
│   │   ├── EnvironmentCommand.php
│   │   └── InstallMakeCommand.php
│   ├── Concerns/
│   │   └── UserReference/
│   │       └── HasUserReference.php    # Trait for user-people linking
│   ├── Contracts/
│   │   ├── ModulePeople.php            # Main contract interface
│   │   ├── Data/                       # DTO contracts
│   │   │   ├── PeopleData.php
│   │   │   ├── FamilyRelationshipData.php
│   │   │   └── ...
│   │   └── Schemas/                    # Schema contracts
│   │       ├── People.php
│   │       ├── FamilyRelationship.php
│   │       └── ...
│   ├── Data/                           # Data Transfer Objects (DTOs)
│   │   ├── PeopleData.php              # Main people DTO with validation
│   │   ├── PeopleAddressData.php       # Address DTO (KTP, Residence)
│   │   ├── CardIdentityData.php        # Identity card DTO
│   │   ├── FamilyRelationshipData.php  # Family contact DTO
│   │   └── ...
│   ├── Database/
│   │   └── Seeders/
│   │       ├── DatabaseSeeder.php      # Runs all seeders
│   │       ├── EducationSeeder.php     # Indonesian education levels
│   │       ├── FamilyRoleSeeder.php    # Family role types
│   │       ├── PeopleStuffSeeder.php   # General reference data
│   │       └── ReligionSeeder.php      # Indonesian religions
│   ├── Enums/
│   │   ├── FamilyRelationship/
│   │   │   └── Flag.php                # Family relationship types (Indonesian)
│   │   └── People/
│   │       ├── BloodType.php           # Blood types (A, B, O, AB with +/-)
│   │       ├── CardIdentity.php        # Identity card types
│   │       └── Sex.php                 # Male/Female enum
│   ├── Facades/
│   │   └── ModulePeople.php
│   ├── Factories/
│   │   └── People/
│   │       └── PeopleFactory.php
│   ├── Models/
│   │   ├── People/
│   │   │   └── People.php              # Main people model (ULID PK)
│   │   ├── FamilyRelationship/
│   │   │   └── FamilyRelationship.php  # Family relationship model (ULID PK)
│   │   ├── PeopleStuff.php             # Base model for reference data
│   │   ├── Education.php               # Extends PeopleStuff
│   │   ├── FamilyRole.php              # Extends PeopleStuff
│   │   ├── MaritalStatus.php           # Extends PeopleStuff
│   │   └── Religion.php                # Extends PeopleStuff
│   ├── Providers/
│   │   └── CommandServiceProvider.php
│   ├── Resources/
│   │   ├── People/
│   │   │   ├── ViewPeople.php          # List/index representation
│   │   │   └── ShowPeople.php          # Detail representation
│   │   ├── FamilyRelationship/
│   │   ├── Education/
│   │   ├── FamilyRole/
│   │   ├── MaritalStatus/
│   │   └── PeopleStuff/
│   ├── Schemas/
│   │   ├── People.php                  # Main business logic (DANGEROUS: extends PackageManagement)
│   │   ├── FamilyRelationship.php
│   │   ├── Education.php
│   │   ├── FamilyRole.php
│   │   ├── MaritalStatus.php
│   │   ├── PeopleStuff.php
│   │   └── Religion.php
│   ├── Supports/
│   │   └── BaseModulePeople.php        # Base class for schemas (DANGEROUS: extends PackageManagement)
│   ├── ModulePeople.php                # Main module class
│   └── ModulePeopleServiceProvider.php
└── composer.json
```

## Key Models

### People Model

**Table:** `peoples`
**Primary Key:** ULID (string, 26 characters)

```php
// Traits used
use HasUlids, HasCardIdentity, HasAddress, HasProps, HasLocation, HasPhone;

// Key fields
- id (ulid)           - Primary key
- uuid (string|null)  - Optional UUID
- name (string)       - Full name (required)
- first_name (string) - First name
- last_name (string)  - Last name
- sex (enum)          - Male, Female
- dob (date)          - Date of birth
- pob (string)        - Place of birth
- blood_type (enum)   - Blood type
- mother_name (string)
- father_name (string)
- total_children (int)
- religion_id (fk)    - Foreign key to Religion (unicodes table)
- country_id (fk)     - Foreign key to Country
- last_education_id (fk) - Foreign key to Education (unicodes table)
- marital_status_id (fk) - Foreign key to MaritalStatus (unicodes table)
- props (json)        - Flexible JSON properties
```

**Relationships:**
```php
country()            // belongsTo Country
addresses()          // morphMany Address
familyRelationship() // hasOne FamilyRelationship
```

### FamilyRelationship Model

**Table:** `family_relationships`
**Primary Key:** ULID (string)

```php
// Traits used
use HasUlids, HasProps, SoftDeletes;

// Key fields
- id (ulid)           - Primary key
- people_id (fk)      - Foreign key to People
- family_role_id (fk) - Foreign key to FamilyRole (unicodes table)
- name (string)       - Contact person name
- phone (string)      - Contact phone
- sex (enum)          - Male, Female
- reference_type (string) - Polymorphic type
- reference_id (string)   - Polymorphic ID
- props (json)
```

**Relationships:**
```php
people()     // belongsTo People
familyRole() // belongsTo FamilyRole
reference()  // morphTo (polymorphic)
```

### Reference Data Models

All reference data models extend `PeopleStuff` which extends `Unicode`:

- **Education** - Education levels (uses `unicodes` table, flag='Education')
- **Religion** - Religious affiliations (uses `unicodes` table, flag='Religion')
- **MaritalStatus** - Marital status options (uses `unicodes` table, flag='MaritalStatus')
- **FamilyRole** - Family role types (uses `unicodes` table, flag='FamilyRole')

## Enums

### Sex
```php
enum Sex: string
{
    case MALE   = 'Male';
    case FEMALE = 'Female';
}
```

### BloodType
```php
enum BloodType: string
{
    case A           = 'A';
    case B           = 'B';
    case O           = 'O';
    case AB          = 'AB';
    case A_NEGATIVE  = 'A-';
    case B_NEGATIVE  = 'B-';
    case O_NEGATIVE  = 'O-';
    case AB_NEGATIVE = 'AB-';
    case A_POSITIVE  = 'A+';
    case B_POSITIVE  = 'B+';
    case O_POSITIVE  = 'O+';
    case AB_POSITIVE = 'AB+';
}
```

### CardIdentity (Indonesian documents)
```php
enum CardIdentity: string
{
    case NIK      = 'nik';      // Nomor Induk Kependudukan (National ID)
    case SIM      = 'sim';      // Surat Izin Mengemudi (Driver's License)
    case PASSPORT = 'passport';
    case VISA     = 'visa';
    case KK       = 'kk';       // Kartu Keluarga (Family Card)
    case NPWP     = 'npwp';     // Nomor Pokok Wajib Pajak (Tax ID)
}
```

### FamilyRelationship Flag (Indonesian)
```php
enum Flag: string
{
    case ANAK  = 'Anak';   // Child
    case ISTRI = 'Istri';  // Wife
    case SUAMI = 'Suami';  // Husband
    case AYAH  = 'Ayah';   // Father
    case IBU   = 'Ibu';    // Mother
    case KAKEK = 'Kakek';  // Grandfather
    case NENEK = 'Nenek';  // Grandmother
    case PAMAN = 'Paman';  // Uncle
    case TANTE = 'Tante';  // Aunt
}
```

## Data Transfer Objects (DTOs)

### PeopleData

The main DTO with Spatie LaravelData validation:

```php
PeopleData::from([
    'id' => null,                      // Optional: for updates (ULID)
    'name' => 'Full Name',             // Required if first_name/last_name not provided
    'first_name' => 'First',
    'last_name' => 'Last',             // Required if name not provided
    'sex' => 'Male',                   // Enum: Male, Female
    'dob' => '1990-01-15',             // Format: Y-m-d or d-m-Y
    'pob' => 'Jakarta',
    'blood_type' => 'O+',              // BloodType enum values
    'father_name' => 'Father Name',
    'mother_name' => 'Mother Name',
    'religion_id' => null,
    'country_id' => null,
    'last_education_id' => null,
    'marital_status_id' => null,
    'total_children' => 0,
    'is_nationality' => true,
    'phones' => ['08123456789'],
    'address' => PeopleAddressData,    // Nested DTO
    'card_identity' => CardIdentityData, // Nested DTO
    'family_relationship' => FamilyRelationshipData, // Nested DTO
    'props' => []
]);
```

**Important:** The `PeopleData::after()` method automatically:
- Combines `first_name` + `last_name` into `name` if not provided
- Resolves religion, education, marital status props via model lookups
- Handles phone number array population from `props.phone_1`, `props.phone_2`
- Resolves country name from `country_id`

### PeopleAddressData

```php
PeopleAddressData::from([
    'ktp' => AddressData,              // KTP (ID card) address
    'residence' => AddressData,        // Current residence address
    'residence_same_as_ktp' => false   // If true, copies KTP to residence
]);
```

### FamilyRelationshipData

```php
FamilyRelationshipData::from([
    'id' => null,
    'people_id' => 'ulid-here',
    'name' => 'Emergency Contact',
    'phone' => '08123456789',
    'family_role_id' => 'ulid-here',
    'family_role' => FamilyRoleData,   // Alternative: create new role
    'reference_type' => null,
    'reference_id' => null,
    'props' => []
]);
```

## Usage Patterns

### Creating a Person

```php
use Hanafalah\ModulePeople\Contracts\Schemas\People;
use Hanafalah\ModulePeople\Data\PeopleData;

$peopleSchema = app(People::class);

$person = $peopleSchema->prepareStore(PeopleData::from([
    'name' => 'John Doe',
    'sex' => 'Male',
    'dob' => '1990-01-15',
    'pob' => 'Jakarta',
    'blood_type' => 'O+',
    'religion_id' => $religionId,
    'phones' => ['08123456789'],
    'address' => [
        'ktp' => ['name' => 'Jl. Example', ...],
        'residence_same_as_ktp' => true
    ],
    'card_identity' => [
        'nik' => '3201234567890001'
    ],
    'family_relationship' => [
        'name' => 'Jane Doe',
        'phone' => '08111222333',
        'family_role_id' => $familyRoleId
    ]
]));
```

### Using People Model Traits

```php
// HasCardIdentity - from module-card-identity
$person->setCardIdentity('nik', '3201234567890001');
$nik = $person->getCardIdentity('nik');

// HasAddress - from module-regional
$person->setAddress('KTP', $addressData);
$person->setAddress('RESIDENCE', $addressData);

// HasPhone - from laravel-support
$person->setPhone(['08123456789']);

// HasProps - from laravel-has-props
$person->props['custom_field'] = 'value';
$person->save();
```

### HasUserReference Trait

The `HasUserReference` trait links People to User accounts:

```php
// Apply to your model that can be linked to users
class Employee extends Model
{
    use HasUserReference;
}

// Relationships available:
$employee->userReference();   // morphOne UserReference
$employee->userReferences();  // morphMany UserReference
$employee->user();            // hasOneThrough User via UserReference
```

## Caching

Schemas use tag-based caching:

```php
// People schema
protected array $__cache = [
    'index' => [
        'name'     => 'people',
        'tags'     => ['people', 'people-index'],
        'forever'  => true  // Cache indefinitely
    ]
];

// FamilyRelationship schema
protected array $__cache = [
    'index' => [
        'name'     => 'family_relationship',
        'tags'     => ['family_relationship', 'family_relationship-index'],
        'duration' => 24 * 60  // 24 hours in minutes
    ]
];
```

## Database Seeders

Run all seeders:
```bash
docker exec -it wellmed-backbone php artisan db:seed --class="Hanafalah\ModulePeople\Database\Seeders\DatabaseSeeder"
```

Individual seeders:
- **EducationSeeder** - Indonesian education levels (Nil, None, SD, SMP, SMA, D3, S1, S2, S3)
- **ReligionSeeder** - Indonesian religions (Islam, Kristen Protestan, Kristen Katolik, Hindu, Budha, Konghucu, Atheis, Aliran Kepercayaan, Lainnya)
- **FamilyRoleSeeder** - Family role types
- **PeopleStuffSeeder** - General reference data

## Configuration

Config file: `assets/config/config.php`

```php
return [
    'namespace' => 'Hanafalah\\ModulePeople',
    'app' => [
        'contracts' => []
    ],
    'libs' => [
        'model' => 'Models',
        'contract' => 'Contracts',
        'schema' => 'Schemas',
        'database' => 'Database',
        'data' => 'Data',
        'resource' => 'Resources',
        'migration' => '../assets/database/migrations'
    ],
    'database' => [
        'models' => []
    ],
    'commands' => [
        Commands\InstallMakeCommand::class
    ],
    'card_identities' => CardIdentity::cases(),  // Available identity types
];
```

## API Resources

Each model has View and Show resources:
- **ViewResource** - List/index representation (minimal data for lists)
- **ShowResource** - Detail representation (extended data with relations)

### ViewPeople Response
```php
[
    'id' => 'ulid',
    'uuid' => 'uuid|null',
    'name' => 'Full Name',  // Prepends "FNU " if no first_name
    'first_name' => 'First',
    'last_name' => 'Last',
    'dob' => '1990-01-15',
    'pob' => 'Jakarta',
    'age' => 35,            // Calculated from dob
    'sex' => 'Male',
    'card_identity' => ['nik' => '...', 'npwp' => '...'],
    'phone_1' => '08...',
    'phone_2' => '08...'
]
```

### ShowPeople Response (extends ViewPeople)
```php
[
    // All ViewPeople fields plus:
    'profile' => 'url',
    'blood_type' => 'O+',
    'last_education_id' => 'ulid',
    'last_education' => {...},
    'religion_id' => 'ulid',
    'religion' => {...},
    'marital_status_id' => 'ulid',
    'marital_status' => {...},
    'total_children' => 2,
    'email' => 'email@example.com',
    'father_name' => 'Father',
    'mother_name' => 'Mother',
    'is_nationality' => true,
    'nationality' => true,
    'country' => {...},
    'family_relationship' => {...},
    'phones' => [...],
    'address' => {
        'ktp' => {...},
        'residence' => {...}
    }
]
```

## Common Pitfalls

1. **Name validation** - Either `name` OR `first_name`+`last_name` must be provided
2. **Date format** - `dob` accepts both `Y-m-d` and `d-m-Y` formats
3. **Reference data must be seeded first** - Education, Religion, MaritalStatus, FamilyRole
4. **Address handling** - Use `residence_same_as_ktp` flag to auto-copy KTP address
5. **Card identities** - Only types in `config('module-people.card_identities')` are processed
6. **Phones array** - Can be set via `phones` array or `props.phone_1`, `props.phone_2`
7. **ULID primary keys** - All main entities use ULID (26 chars), not auto-increment
8. **Schema classes are DANGEROUS** - They extend `PackageManagement` which can trigger memory issues

## Files to NEVER Auto-Load

These classes extend `PackageManagement` and can cause memory chain loading:
- `src/Schemas/*.php` - All schema classes
- `src/Supports/BaseModulePeople.php` - Base class for schemas
- `src/ModulePeople.php` - Main module class (extends `PackageManagement`)

## Testing Changes

After modifying this module:

```bash
# Clear caches
docker exec -it wellmed-backbone php artisan config:clear
docker exec -it wellmed-backbone php artisan cache:clear

# Reload Octane (REQUIRED in Octane environment)
docker exec -it wellmed-backbone php artisan octane:reload

# Test in dependent applications
docker exec -it wellmed-hq php artisan octane:reload

# Monitor for memory issues
docker logs wellmed-backbone 2>&1 | grep -i "memory\|fatal"
```

## Modification Checklist

Before modifying module-people:

- [ ] Change doesn't affect `ModulePeopleServiceProvider->register()` behavior
- [ ] No new classes extend `PackageManagement` unnecessarily
- [ ] No circular imports added
- [ ] Tested with `php artisan config:clear`
- [ ] Tested boot in wellmed-backbone container
- [ ] Memory stays under 512MB during boot
- [ ] All dependent modules still work
- [ ] Octane reload works without errors
