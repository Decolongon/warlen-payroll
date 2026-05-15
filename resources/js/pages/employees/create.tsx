import { Head, useForm, usePage, router } from '@inertiajs/react';
import {
    Search, ChevronDown, User, Briefcase, MapPin, Calendar,
    Clock, LoaderCircle, PersonStanding, Shield, BookOpen,
    Home, Users, Plus, X,
    Award,
    Gavel,
} from 'lucide-react';
import { useEffect, useState, useRef } from 'react';
import { store } from '@/actions/App/Http/Controllers/EmployeeController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Employees', href: '/employees' },
    { title: 'Create', href: '/employees/create' },
];

interface Site {
    id: number;
    site_name: string;
    branch_id: number;
}

interface Branch {
    id: number;
    branch_name: string;
    branch_address?: string;
}

interface Props {
    positions: any[];
    branches: Branch[];
    site: Site[];
}

const STATUS_LABEL: Record<string, string> = {
    active: 'Active',
    newly_hired: 'Newly Hired',
    end_of_contract: 'End of Contract',
    awol: 'AWOL',
    terminated: 'Terminated',
    resigned: 'Resigned',
};

const getStatusFromDates = (start: string, end: string): string => {
    if (!start || !end) return 'newly_hired';
    const today = new Date();
    const startDate = new Date(start);
    const endDate = new Date(end);
    today.setHours(0, 0, 0, 0);
    startDate.setHours(0, 0, 0, 0);
    endDate.setHours(0, 0, 0, 0);
    return today >= startDate && today <= endDate ? 'active' : 'newly_hired';
};

const computeAge = (dob: string): string => {
    if (!dob) return '';
    const birth = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    return age >= 0 ? String(age) : '';
};

// NEW: Human-readable duration formatter
function formatDateRange(startDateStr: string | null, endDateStr: string | null): string {
    if (!startDateStr || !endDateStr) return 'Undefined';

    const start = new Date(startDateStr);
    const end = new Date(endDateStr);
    if (isNaN(start.getTime()) || isNaN(end.getTime())) return 'Invalid dates';
    if (end < start) return 'End date before start date';

    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    if (days < 0) {
        months--;
        const lastMonth = new Date(end.getFullYear(), end.getMonth(), 0);
        days += lastMonth.getDate();
    }
    if (months < 0) {
        years--;
        months += 12;
    }

    const parts: string[] = [];
    if (years > 0) parts.push(`${years} year${years !== 1 ? 's' : ''}`);
    if (months > 0) parts.push(`${months} month${months !== 1 ? 's' : ''}`);
    if (days > 0) parts.push(`${days} day${days !== 1 ? 's' : ''}`);

    return parts.length ? parts.join(', ') : '0 days';
}

function FormSection({
    icon: Icon,
    title,
    children,
    index = 0,
}: {
    icon: React.ElementType;
    title: string;
    children: React.ReactNode;
    index?: number;
}) {
    return (
        <div
            className="form-section space-y-4 rounded-2xl border border-border bg-card p-5 shadow-sm"
            style={{ animationDelay: `${index * 80}ms` }}
        >
            <div className="flex items-center gap-2 border-b border-border pb-3">
                <div className="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                    <Icon className="h-4 w-4 text-primary" />
                </div>
                <h3 className="text-sm font-bold text-foreground">{title}</h3>
            </div>
            {children}
        </div>
    );
}

interface DropdownItem {
    id: number | string;
    name: string;
}

interface DropdownProps {
    label: string;
    items: DropdownItem[];
    selectedId: string;
    onSelect: (id: string, name: string) => void;
    searchValue: string;
    onSearchChange: (value: string) => void;
    placeholder?: string;
    disabled?: boolean;
    error?: string;
    required?: boolean;
    searchPlaceholder?: string;
    showAllResults?: boolean;
}

function SearchableDropdown({
    label,
    items,
    selectedId,
    onSelect,
    searchValue,
    onSearchChange,
    placeholder = 'Select an option',
    disabled = false,
    error,
    required = false,
    searchPlaceholder = 'Search...',
    showAllResults = false,
}: DropdownProps) {
    const [isOpen, setIsOpen] = useState(false);
    const selectedItem = items.find(i => i.id.toString() === selectedId);

    return (
        <div className="space-y-2">
            <Label className="text-sm font-semibold">
                {label} {required && <span className="text-destructive">*</span>}
            </Label>
            <div className="relative">
                <div
                    className={`flex cursor-pointer items-center justify-between rounded-xl border-2 bg-background px-4 py-2.5 transition-all ${isOpen
                        ? 'border-primary ring-2 ring-primary/20'
                        : 'border-border hover:border-primary/50'
                        } ${disabled ? 'cursor-not-allowed opacity-50' : ''}`}
                    onClick={() => !disabled && setIsOpen(!isOpen)}
                >
                    <span className={`text-sm ${!selectedItem ? 'text-muted-foreground' : 'text-foreground'}`}>
                        {selectedItem?.name || placeholder}
                    </span>
                    <ChevronDown
                        className={`h-4 w-4 text-muted-foreground transition-transform ${isOpen ? 'rotate-180' : ''}`}
                    />
                </div>

                {isOpen && !disabled && (
                    <>
                        <div className="absolute z-10 mt-2 w-full rounded-xl border border-border bg-card shadow-lg">
                            <div className="border-b border-border p-2">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        value={searchValue}
                                        onChange={e => onSearchChange(e.target.value)}
                                        placeholder={searchPlaceholder}
                                        className="pl-9"
                                        autoFocus
                                        onClick={e => e.stopPropagation()}
                                    />
                                </div>
                            </div>
                            <div className="max-h-60 overflow-auto p-1">
                                {items.length > 0 ? (
                                    items.map(item => (
                                        <div
                                            key={item.id}
                                            className="cursor-pointer rounded-lg px-3 py-2 text-sm transition-colors hover:bg-muted"
                                            onClick={() => {
                                                onSelect(item.id.toString(), item.name);
                                                setIsOpen(false);
                                            }}
                                        >
                                            {item.name}
                                        </div>
                                    ))
                                ) : (
                                    <div className="px-3 py-2 text-sm text-muted-foreground">No results found</div>
                                )}
                                {!showAllResults && items.length === 5 && (
                                    <div className="mt-1 border-t px-3 pb-2 pt-2 text-xs text-muted-foreground">
                                        Showing top 5 results. Use search to find more.
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="fixed inset-0 z-0" onClick={() => setIsOpen(false)} />
                    </>
                )}
            </div>
            {error && <InputError message={error} />}
        </div>
    );
}

function SkillsInput({
    skills,
    onChange,
    error,
    placeholder = "e.g., Plumbing, Electrical, Carpentry, Welding",
}: {
    skills: string[];
    onChange: (skills: string[]) => void;
    error?: string;
    placeholder?: string;
}) {
    const [input, setInput] = useState('');

    const add = () => {
        const trimmed = input.trim();
        if (!trimmed || skills.includes(trimmed) || skills.length >= 20) return;
        onChange([...skills, trimmed]);
        setInput('');
    };

    const remove = (skill: string) => onChange(skills.filter(s => s !== skill));

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            add();
        }
    };

    return (
        <div className="space-y-2">
            <Label className="text-sm font-semibold">Skills</Label>
            <div className="flex gap-2">
                <Input
                    value={input}
                    onChange={e => setInput(e.target.value)}
                    onKeyDown={handleKeyDown}
                    placeholder={placeholder}
                    className="rounded-xl"
                    maxLength={50}
                />
                <Button
                    type="button"
                    variant="outline"
                    size="icon"
                    onClick={add}
                    disabled={!input.trim() || skills.length >= 20}
                    className="shrink-0 rounded-xl"
                >
                    <Plus className="h-4 w-4" />
                </Button>
            </div>
            {skills.length > 0 && (
                <div className="flex flex-wrap gap-2 pt-1">
                    {skills.map(skill => (
                        <span
                            key={skill}
                            className="inline-flex items-center gap-1 rounded-lg bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary"
                        >
                            {skill}
                            <button
                                type="button"
                                onClick={() => remove(skill)}
                                className="ml-0.5 rounded hover:text-destructive"
                            >
                                <X className="h-3 w-3" />
                            </button>
                        </span>
                    ))}
                </div>
            )}
            <p className="text-xs text-muted-foreground">{skills.length}/20 skills added</p>
            {error && <InputError message={error} />}
        </div>
    );
}

function CertificateInput({
    certificates,
    onChange,
    error,
    placeholder = "e.g., TESDA NC II, OSHA Certified",
}: {
    certificates: string[];
    onChange: (certificates: string[]) => void;
    error?: string;
    placeholder?: string;
}) {
    const [input, setInput] = useState('');

    const add = () => {
        const trimmed = input.trim();
        if (!trimmed || certificates.includes(trimmed) || certificates.length >= 10) return;
        onChange([...certificates, trimmed]);
        setInput('');
    };

    const remove = (cert: string) => onChange(certificates.filter(c => c !== cert));

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            add();
        }
    };

    return (
        <div className="space-y-2">
            <Label className="text-sm font-semibold">Certificate / Qualification</Label>
            <div className="flex gap-2">
                <Input
                    value={input}
                    onChange={e => setInput(e.target.value)}
                    onKeyDown={handleKeyDown}
                    placeholder={placeholder}
                    className="rounded-xl"
                    maxLength={100}
                />
                <Button
                    type="button"
                    variant="outline"
                    size="icon"
                    onClick={add}
                    disabled={!input.trim() || certificates.length >= 10}
                    className="shrink-0 rounded-xl"
                >
                    <Plus className="h-4 w-4" />
                </Button>
            </div>
            {certificates.length > 0 && (
                <div className="flex flex-wrap gap-2 pt-1">
                    {certificates.map(cert => (
                        <span
                            key={cert}
                            className="inline-flex items-center gap-1 rounded-lg bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-600 dark:text-amber-400"
                        >
                            {cert}
                            <button
                                type="button"
                                onClick={() => remove(cert)}
                                className="ml-0.5 rounded hover:text-destructive"
                            >
                                <X className="h-3 w-3" />
                            </button>
                        </span>
                    ))}
                </div>
            )}
            <p className="text-xs text-muted-foreground">{certificates.length}/10 certificates added</p>
            {error && <InputError message={error} />}
        </div>
    );
}

function PhoneInput({
    value,
    onChange,
    placeholder = '9XX XXX XXXX',
    error,
}: {
    value: string;
    onChange: (val: string) => void;
    placeholder?: string;
    error?: string;
}) {
    const display = value ? value.replace('+63', '') : '';
    const handleChange = (raw: string) => {
        const digits = raw.replace(/\D/g, '').slice(0, 10);
        onChange(digits ? `+63${digits}` : '');
    };
    return (
        <>
            <div className="flex">
                <span className="inline-flex items-center rounded-l-xl border border-r-0 border-border bg-muted px-3 text-sm text-muted-foreground">
                    +63
                </span>
                <Input
                    type="text"
                    value={display}
                    onChange={e => handleChange(e.target.value)}
                    placeholder={placeholder}
                    maxLength={10}
                    className="rounded-l-none rounded-r-xl"
                />
            </div>
            {error && <InputError message={error} />}
        </>
    );
}

export default function Create({ positions, branches, site = [] }: Props) {
    const [availableSites, setAvailableSites] = useState<Site[]>([]);
    const [positionSearch, setPositionSearch] = useState('');
    const [branchSearch, setBranchSearch] = useState('');
    const [siteSearch, setSiteSearch] = useState('');
    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        emp_code: '',
        employee_number: '',
        avatar: null as File | null,
        position_id: '',
        branch_id: '',
        site_id: '',
        contract_start_date: '',
        contract_end_date: '',
        pay_frequency: '',
        employee_status: 'newly_hired',
        emergency_contact_number: '',
        contact_person: '',
        contact_person_number: '',
        sss_number: '',
        pagibig_number: '',
        philhealth_number: '',
        tin_number: '',
        gender: 'male',
        age: '',
        dob: '',
        mother_name: '',
        father_name: '',
        educ_attainment: '',
        certificate: [] as string[],
        permanent_address: '',
        present_address: '',
        skills: [] as string[],
    });

    // Filter sites by selected branch
    useEffect(() => {
        if (data.branch_id) {
            const filtered = site.filter(s => s.branch_id === parseInt(data.branch_id));
            setAvailableSites(filtered);
            if (data.site_id && !filtered.some(s => s.id === parseInt(data.site_id))) {
                setData('site_id', '');
                setSiteSearch('');
            }
        } else {
            setAvailableSites([]);
            setData('site_id', '');
            setSiteSearch('');
        }
    }, [data.branch_id]);

    // Auto‑compute age from DOB
    useEffect(() => {
        setData('age', computeAge(data.dob));
    }, [data.dob]);

    // Auto‑derive employee_status from contract dates
    useEffect(() => {
        setData(
            'employee_status',
            data.contract_start_date && data.contract_end_date
                ? getStatusFromDates(data.contract_start_date, data.contract_end_date)
                : 'newly_hired',
        );
    }, [data.contract_start_date, data.contract_end_date]);

    // (Removed calculatedDuration state – we now compute formatted string directly)

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;
        setData('avatar', file);
        setAvatarPreview(URL.createObjectURL(file));
    };

    const handleRemoveAvatar = () => {
        setData('avatar', null);
        setAvatarPreview(null);
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    const handleGovNumberChange = (
        field: 'sss_number' | 'pagibig_number' | 'philhealth_number' | 'tin_number',
        value: string,
        maxLength: number,
    ) => {
        setData(field, value.replace(/[^0-9\-]/g, '').slice(0, maxLength));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url);
    };

    const filteredPositions = positions
        .map(p => ({ id: p.id, name: p.pos_name }))
        .filter(p => p.name.toLowerCase().includes(positionSearch.toLowerCase()))
        .slice(0, 5);

    const filteredBranches = branches
        .map(b => ({ id: b.id, name: b.branch_name }))
        .filter(b => b.name.toLowerCase().includes(branchSearch.toLowerCase()))
        .slice(0, 5);

    const filteredSites = availableSites
        .map(s => ({ id: s.id, name: s.site_name }))
        .filter(s => s.name.toLowerCase().includes(siteSearch.toLowerCase()))
        .slice(0, 5);

    // Human-readable contract duration
    const contractDuration = formatDateRange(data.contract_start_date, data.contract_end_date);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Employee" />

            <style>{`
                @keyframes formFadeUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
                .form-section { animation: formFadeUp 0.45s cubic-bezier(0.22,1,0.36,1) both; }
            `}</style>

            <div className="min-h-screen py-8 md:py-10">
                <div className="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    {/* Page header */}
                    <div className="mb-8 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary shadow-md">
                                <User className="h-5 w-5 text-primary-foreground" />
                            </div>
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">
                                    HR Management
                                </p>
                                <h1 className="text-xl font-extrabold tracking-tight text-foreground">
                                    Create New Employee
                                </h1>
                            </div>
                        </div>
                        <Button
                            variant="outline"
                            onClick={() => router.get('/employees')}
                            className="rounded-xl border-2 border-primary text-primary hover:bg-primary hover:text-primary-foreground"
                        >
                            Cancel
                        </Button>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6" encType="multipart/form-data">
                        {/* 1. Avatar */}
                        <FormSection icon={PersonStanding} title="Avatar" index={1}>
                            <div className="grid gap-2">
                                <Label>Profile picture</Label>
                                <div className="flex items-center gap-4">
                                    <div className="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded-full bg-muted">
                                        {avatarPreview ? (
                                            <img src={avatarPreview} alt="Profile preview" className="h-full w-full object-cover" />
                                        ) : (
                                            <div className="flex h-full w-full items-center justify-center bg-neutral-100 text-neutral-400 dark:bg-neutral-800">
                                                <svg className="h-10 w-10" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                </svg>
                                            </div>
                                        )}
                                    </div>
                                    <div className="flex flex-col gap-2">
                                        <div className="flex gap-2">
                                            <Button type="button" variant="outline" size="sm" onClick={() => fileInputRef.current?.click()} disabled={processing}>
                                                Choose image
                                            </Button>
                                            {avatarPreview && (
                                                <Button type="button" variant="destructive" size="sm" onClick={handleRemoveAvatar} disabled={processing}>
                                                    Remove
                                                </Button>
                                            )}
                                        </div>
                                        <p className="text-xs text-muted-foreground">Square image, at least 200×200 px. Max 2 MB.</p>
                                    </div>
                                    <Input
                                        ref={fileInputRef}
                                        id="avatar"
                                        type="file"
                                        name="avatar"
                                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                        onChange={handleAvatarChange}
                                        className="hidden"
                                    />
                                </div>
                                {errors.avatar && <InputError className="mt-2" message={errors.avatar} />}
                            </div>
                        </FormSection>

                        {/* 2. User Details */}
                        <FormSection icon={User} title="User Details" index={2}>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Employee Code <span className="text-destructive">*</span></Label>
                                    <Input value={data.emp_code} onChange={e => setData('emp_code', e.target.value)} placeholder="e.g., 1001" className="rounded-xl" />
                                    <InputError message={errors.emp_code} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Full Name <span className="text-destructive">*</span></Label>
                                    <Input value={data.name} onChange={e => setData('name', e.target.value)} placeholder="Juan Dela Cruz" className="rounded-xl" />
                                    <InputError message={errors.name} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Email <span className="text-destructive">*</span></Label>
                                    <Input type="email" value={data.email} onChange={e => setData('email', e.target.value)} placeholder="juan@example.com" className="rounded-xl" />
                                    <InputError message={errors.email} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Password <span className="text-destructive">*</span></Label>
                                    <Input type="password" value={data.password} onChange={e => setData('password', e.target.value)} placeholder="••••••••" className="rounded-xl" />
                                    <InputError message={errors.password} />
                                </div>
                            </div>
                        </FormSection>

                        {/* 3. Personal Information */}
                        <FormSection icon={Users} title="Personal Information" index={3}>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Gender</Label>
                                    <select
                                        value={data.gender}
                                        onChange={e => setData('gender', e.target.value)}
                                        className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none"
                                    >
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    <InputError message={errors.gender} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Date of Birth</Label>
                                    <input
                                        type="date"
                                        value={data.dob}
                                        onChange={e => setData('dob', e.target.value)}
                                        max={new Date().toISOString().split('T')[0]}
                                        className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/20"
                                    />
                                    <InputError message={errors.dob} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Age</Label>
                                    <div className="flex h-11 items-center rounded-xl border-2 border-border bg-muted/30 px-4 text-sm text-foreground">
                                        {data.age ? `${data.age} years old` : <span className="text-muted-foreground">Auto-computed from date of birth</span>}
                                    </div>
                                    <InputError message={errors.age} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Educational Attainment</Label>
                                    <Input
                                        value={data.educ_attainment}
                                        onChange={e => setData('educ_attainment', e.target.value)}
                                        placeholder="e.g., High School Graduate"
                                        className="rounded-xl"
                                        maxLength={100}
                                    />
                                    <InputError message={errors.educ_attainment} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Mother's Name</Label>
                                    <Input
                                        value={data.mother_name}
                                        onChange={e => setData('mother_name', e.target.value)}
                                        placeholder="e.g., Maria Santos"
                                        className="rounded-xl"
                                        maxLength={100}
                                    />
                                    <InputError message={errors.mother_name} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Father's Name</Label>
                                    <Input
                                        value={data.father_name}
                                        onChange={e => setData('father_name', e.target.value)}
                                        placeholder="e.g., Jose Santos"
                                        className="rounded-xl"
                                        maxLength={100}
                                    />
                                    <InputError message={errors.father_name} />
                                </div>
                                {/* Certificate - Left Column */}
                                <div className="space-y-2">
                                    <CertificateInput
                                        certificates={data.certificate}
                                        onChange={certs => setData('certificate', certs)}
                                        error={errors.certificate}
                                    />
                                </div>

                                {/* Skills - Right Column */}
                                <div className="space-y-2">
                                    <SkillsInput
                                        skills={data.skills}
                                        onChange={skills => setData('skills', skills)}
                                        error={errors.skills as string | undefined}
                                    />
                                </div>
                            </div>
                        </FormSection>

                        {/* 4. Address */}
                        <FormSection icon={Home} title="Address" index={4}>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Permanent Address</Label>
                                    <textarea
                                        value={data.permanent_address}
                                        onChange={e => setData('permanent_address', e.target.value)}
                                        placeholder="Barangay, Municipality, Province"
                                        rows={3}
                                        maxLength={500}
                                        className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/20 resize-none"
                                    />
                                    <InputError message={errors.permanent_address} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Present Address</Label>
                                    <textarea
                                        value={data.present_address}
                                        onChange={e => setData('present_address', e.target.value)}
                                        placeholder="Barangay, Municipality, Province"
                                        rows={3}
                                        maxLength={500}
                                        className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/20 resize-none"
                                    />
                                    <InputError message={errors.present_address} />
                                </div>
                            </div>
                        </FormSection>

                        {/* 5. Employee Details */}
                        <FormSection icon={Briefcase} title="Employee Details" index={5}>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Contact Number <span className="text-destructive">*</span></Label>
                                    <PhoneInput
                                        value={data.employee_number}
                                        onChange={val => setData('employee_number', val)}
                                        error={errors.employee_number}
                                    />
                                </div>
                                <SearchableDropdown
                                    label="Position"
                                    items={filteredPositions}
                                    selectedId={data.position_id}
                                    onSelect={(id, name) => { setData('position_id', id); setPositionSearch(name); }}
                                    searchValue={positionSearch}
                                    onSearchChange={setPositionSearch}
                                    required
                                    error={errors.position_id}
                                    placeholder="Select a position"
                                    searchPlaceholder="Search positions..."
                                />
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Pay Frequency <span className="text-destructive">*</span></Label>
                                    <select
                                        value={data.pay_frequency}
                                        onChange={e => setData('pay_frequency', e.target.value)}
                                        className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none"
                                    >
                                        <option value="">Select pay frequency</option>
                                        <option value="weekender">Weekender</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="semi_monthly">Semi-Monthly</option>
                                    </select>
                                    <InputError message={errors.pay_frequency} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Status</Label>
                                    <select
                                        value={data.employee_status}
                                        onChange={e => setData('employee_status', e.target.value)}
                                        className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none"
                                    >
                                        {Object.entries(STATUS_LABEL).map(([value, label]) => (
                                            <option key={value} value={value}>{label}</option>
                                        ))}
                                    </select>
                                    <InputError message={errors.employee_status} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Emergency Contact Number <span className="text-destructive">*</span></Label>
                                    <PhoneInput
                                        value={data.emergency_contact_number}
                                        onChange={val => setData('emergency_contact_number', val)}
                                        error={errors.emergency_contact_number}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Contact Person</Label>
                                    <Input
                                        value={data.contact_person}
                                        onChange={e => setData('contact_person', e.target.value)}
                                        placeholder="e.g., Maria Santos"
                                        className="rounded-xl"
                                        maxLength={100}
                                    />
                                    <InputError message={errors.contact_person} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Contact Person Number</Label>
                                    <PhoneInput
                                        value={data.contact_person_number}
                                        onChange={val => setData('contact_person_number', val)}
                                        error={errors.contact_person_number}
                                    />
                                </div>
                            </div>
                        </FormSection>

                        {/* 6. Government Numbers */}
                        <FormSection icon={Shield} title="Government Numbers" index={6}>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">SSS Number </Label>
                                    <Input
                                        type="password"
                                        value={data.sss_number}
                                        onChange={e => handleGovNumberChange('sss_number', e.target.value, 15)}
                                        placeholder="e.g., 12-3456789-1"
                                        maxLength={15}
                                        className="rounded-xl"
                                    />
                                    <InputError message={errors.sss_number} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">Pag-IBIG Membership ID </Label>
                                    <Input
                                        type="password"
                                        value={data.pagibig_number}
                                        onChange={e => handleGovNumberChange('pagibig_number', e.target.value, 15)}
                                        placeholder="e.g., 9102-1234-5678"
                                        maxLength={15}
                                        className="rounded-xl"
                                    />
                                    <InputError message={errors.pagibig_number} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">PhilHealth ID Number (PIN)</Label>
                                    <Input
                                        type="password"
                                        value={data.philhealth_number}
                                        onChange={e => handleGovNumberChange('philhealth_number', e.target.value, 15)}
                                        placeholder="e.g., 9102-1234-5678"
                                        maxLength={15}
                                        className="rounded-xl"
                                    />
                                    <InputError message={errors.philhealth_number} />
                                </div>
                                <div className="space-y-2">
                                    <Label className="text-sm font-semibold">TIN Number </Label>
                                    <Input
                                        type="password"
                                        value={data.tin_number}
                                        onChange={e => handleGovNumberChange('tin_number', e.target.value, 15)}
                                        placeholder="e.g., 123-456-789-000"
                                        maxLength={15}
                                        className="rounded-xl"
                                    />
                                    <InputError message={errors.tin_number} />
                                </div>
                            </div>
                        </FormSection>


                        {/* 8. Location Assignment */}
                        <FormSection icon={MapPin} title="Assignment & Contract Period" index={7}>
                            <div className="space-y-6">
                                {/* Location row */}
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <SearchableDropdown
                                        label="Branch"
                                        items={filteredBranches}
                                        selectedId={data.branch_id}
                                        onSelect={(id, name) => {
                                            setData('branch_id', id);
                                            setBranchSearch(name);
                                        }}
                                        searchValue={branchSearch}
                                        onSearchChange={setBranchSearch}
                                        required
                                        error={errors.branch_id}
                                        placeholder="Select a branch"
                                        searchPlaceholder="Search branches..."
                                    />

                                    {data.branch_id && (
                                        <SearchableDropdown
                                            label="Site"
                                            items={filteredSites}
                                            selectedId={data.site_id}
                                            onSelect={(id, name) => {
                                                setData('site_id', id);
                                                setSiteSearch(name);
                                            }}
                                            searchValue={siteSearch}
                                            onSearchChange={setSiteSearch}
                                            required
                                            error={errors.site_id}
                                            placeholder={availableSites.length === 0 ? 'No sites for this branch' : 'Select a site'}
                                            searchPlaceholder="Search sites..."
                                            disabled={availableSites.length === 0}
                                        />
                                    )}
                                </div>

                                {/* Contract period row */}
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="space-y-2">
                                        <Label className="text-sm font-semibold">
                                            Start Date <span className="text-destructive">*</span>
                                        </Label>
                                        <input
                                            type="date"
                                            value={data.contract_start_date}
                                            onChange={(e) => setData('contract_start_date', e.target.value)}
                                            className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/20"
                                        />
                                        <InputError message={errors.contract_start_date} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label className="text-sm font-semibold">End Date</Label>
                                        <input
                                            type="date"
                                            value={data.contract_end_date}
                                            onChange={(e) => setData('contract_end_date', e.target.value)}
                                            min={data.contract_start_date || undefined}
                                            className="w-full rounded-xl border-2 border-border bg-background px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/20"
                                        />
                                        <InputError message={errors.contract_end_date} />
                                    </div>

                                    {/* Human‑readable duration */}
                                    <div className="sm:col-span-2">
                                        <Label className="text-sm font-semibold">Contract Duration</Label>
                                        <div className="flex h-11 items-center rounded-xl border-2 border-border bg-muted/30 px-4 text-sm text-foreground">
                                            {contractDuration}
                                        </div>
                                        <p className="text-xs text-muted-foreground">Auto‑calculated from contract dates</p>
                                    </div>
                                </div>
                            </div>
                        </FormSection>

                        {/* Submit */}
                        <div className="flex justify-end pt-4">
                            <Button
                                type="submit"
                                disabled={processing}
                                className="min-w-[140px] rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-primary-foreground transition-all hover:brightness-110 active:scale-95 disabled:opacity-60"
                            >
                                {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                {processing ? 'Creating…' : 'Create Employee'}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}