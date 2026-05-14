// resources/js/pages/employees/show.tsx
import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft, Edit, Trash2, Mail, Phone, Calendar, MapPin,
    Briefcase, CreditCard, Clock, User, Shield,
    Award, Home, BookOpen,
} from 'lucide-react';
import { useState } from 'react';
import EmployeeController from '@/actions/App/Http/Controllers/EmployeeController';
import { Button } from '@/components/ui/button';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle, DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

/* ─────────────────────────────────────────────────────────────
   Types
   ───────────────────────────────────────────────────────────── */
interface Employee {
    id: number;
    slug_emp: string;
    emp_code: number;
    avatar: string;
    employee_number: string;
    emergency_contact_number: string;
    contract_start_date: string;
    contract_end_date: string;
    pay_frequency: 'weekender' | 'monthly' | 'semi_monthly';
    employee_status: 'active' | 'inactive';
    created_at: string;
    updated_at: string;
    // Government IDs
    sss_number?: string;
    pagibig_number?: string;
    philhealth_number?: string;
    // Relations
    position: { id: number; pos_name: string; deleted_at: string | null } | null;
    branch:   { id: number; branch_name: string; branch_address: string } | null;
    sites:    { id: number; site_name: string } | null;
    user:     { id: number; name: string; email: string; avatar: string };
    // Contact
    contact_person?: string;
    contact_person_number?: string;
    // Bio
    age?: number | string;
    gender?: string;
    dob?: string;
    mother_name?: string;
    father_name?: string;
    educ_attainment?: string;
    certificate?: string;
    // Address
    permanent_address?: string;
    present_address?: string;
    // Skills (JSON column)
    skills?: string[] | string | null;
}

interface PageProps { employee: Employee }

/* ─────────────────────────────────────────────────────────────
   Helpers
   ───────────────────────────────────────────────────────────── */
const formatDate = (d?: string | null): string => {
    if (!d) return '—';
    try {
        return new Date(d).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric',
        });
    } catch { return d; }
};

const formatPayFrequency = (f: string): string =>
    f.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());

const stringToHslColor = (str: string, s = 65, l = 48): string => {
    let hash = 0;
    for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
    return `hsl(${((hash % 360) + 360) % 360}, ${s}%, ${l}%)`;
};
const getAvatarBg  = (name: string) => ({ backgroundColor: stringToHslColor(name), color: '#fff' });
const getInitials  = (name?: string) =>
    name?.split(' ').map(p => p[0]?.toUpperCase()).filter(Boolean).join('').slice(0, 2) || '?';

const computeDuration = (startStr?: string | null): string => {
    if (!startStr) return '—';
    const start = new Date(startStr);
    if (isNaN(start.getTime())) return '—';
    const now    = new Date();
    let years    = now.getFullYear() - start.getFullYear();
    let months   = now.getMonth()    - start.getMonth();
    const days   = now.getDate()     - start.getDate();
    if (days   < 0) months--;
    if (months < 0) { years--; months += 12; }
    if (years === 0 && months === 0) return `${Math.max(0, days)} day${days !== 1 ? 's' : ''}`;
    if (years === 0)  return `${months} month${months !== 1 ? 's' : ''}`;
    if (months === 0) return `${years} year${years !== 1 ? 's' : ''}`;
    return `${years} yr${years !== 1 ? 's' : ''}, ${months} mo${months !== 1 ? 's' : ''}`;
};

const normaliseSkills = (raw?: string[] | string | null): string[] => {
    if (!raw) return [];
    if (Array.isArray(raw)) return raw.filter(Boolean);
    try {
        const p = JSON.parse(raw);
        return Array.isArray(p) ? p.filter(Boolean) : [];
    } catch {
        return raw.split(',').map(s => s.trim()).filter(Boolean);
    }
};

/* ─────────────────────────────────────────────────────────────
   Micro-components
   ───────────────────────────────────────────────────────────── */
function StatusBadge({ status }: { status: 'active' | 'inactive' }) {
    const on = status === 'active';
    return (
        <span className={`inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border ${
            on  ? 'bg-emerald-500/15 text-emerald-300 border-emerald-400/30'
                : 'bg-orange-500/15  text-orange-300  border-orange-400/30'
        }`}>
            <span className={`w-1.5 h-1.5 rounded-full ${on ? 'bg-emerald-400' : 'bg-orange-400'}`} />
            {on ? 'Active' : 'Inactive'}
        </span>
    );
}

function FreqBadge({ frequency }: { frequency: string }) {
    return (
        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-[#1d4791]/10 text-[#1d4791] border border-[#1d4791]/20">
            <CreditCard className="h-3 w-3" />
            {formatPayFrequency(frequency)}
        </span>
    );
}

/* ─────────────────────────────────────────────────────────────
   Section shell — standalone card (no outer wrapper)
   ───────────────────────────────────────────────────────────── */
function Section({ icon: Icon, title, children, delay = 0 }: {
    icon: React.ElementType;
    title: string;
    children: React.ReactNode;
    delay?: number;
}) {
    return (
        <div
            className="rounded-xl overflow-hidden border border-slate-200 bg-white shadow-sm flex flex-col"
            style={{ animation: 'empFadeUp 0.4s ease both', animationDelay: `${delay}ms` }}
        >
            {/* Navy section header */}
            <div className="flex items-center gap-2 px-4 py-2.5 bg-[#1d4791]">
                <Icon className="h-3.5 w-3.5 text-white/75 flex-shrink-0" />
                <h3 className="text-[10px] font-bold tracking-widest uppercase text-white">{title}</h3>
            </div>
            <dl className="p-4 space-y-3 flex-1">{children}</dl>
        </div>
    );
}

/* ─────────────────────────────────────────────────────────────
   InfoRow — single-line label : value
   ───────────────────────────────────────────────────────────── */
function InfoRow({ label, value, isComponent = false }: {
    label: string;
    value: React.ReactNode;
    isComponent?: boolean;
}) {
    const isEmpty = value === null || value === undefined || value === '';
    return (
        <div className="flex items-start justify-between gap-4 py-0.5">
            <dt className="text-[11px] text-slate-400 shrink-0 leading-5">{label}</dt>
            <dd className={`text-xs font-medium leading-5 text-right ${
                isEmpty ? 'text-slate-300 italic' : 'text-slate-800'
            } ${isComponent ? '' : 'break-words max-w-[58%]'}`}>
                {isEmpty ? '—' : value}
            </dd>
        </div>
    );
}

/* ─────────────────────────────────────────────────────────────
   InfoBlock — label above, multiline value below
   ───────────────────────────────────────────────────────────── */
function InfoBlock({ label, value }: { label: string; value?: string | null }) {
    const empty = !value?.trim();
    return (
        <div className="space-y-1.5">
            <dt className="text-[11px] text-slate-400">{label}</dt>
            <dd className={`text-xs font-medium leading-relaxed whitespace-pre-line rounded-lg px-3 py-2 border ${
                empty
                    ? 'text-slate-300 italic bg-slate-50 border-slate-100'
                    : 'text-slate-800 bg-slate-50 border-slate-100'
            }`}>
                {empty ? 'Not provided' : value}
            </dd>
        </div>
    );
}

/* ─────────────────────────────────────────────────────────────
   SkillChips
   ───────────────────────────────────────────────────────────── */
function SkillChips({ skills }: { skills: string[] }) {
    if (skills.length === 0) {
        return <span className="text-xs text-slate-300 italic">No skills listed</span>;
    }
    return (
        <div className="flex flex-wrap gap-1.5">
            {skills.map((s, i) => (
                <span key={i} className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#1d4791]/8 text-[#1d4791] border border-[#1d4791]/15">
                    {s}
                </span>
            ))}
        </div>
    );
}

/* ─────────────────────────────────────────────────────────────
   Main page
   ───────────────────────────────────────────────────────────── */
export default function Show({ employee }: PageProps) {
    const { delete: destroy } = useForm();
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);
    const [isDeleting,   setIsDeleting]   = useState(false);

    const handleDelete = () => {
        setIsDeleting(true);
        destroy(EmployeeController.destroy(employee.slug_emp).url, {
            onFinish: () => { setIsDeleting(false); setIsDeleteOpen(false); },
        });
    };

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Employees', href: '/employees' },
        { title: employee.user?.name ?? `Employee #${employee.emp_code}`, href: `/employees/${employee.slug_emp}` },
    ];

    const avatarUrl    = employee.avatar
        ? employee.avatar.startsWith('http') ? employee.avatar : `/storage/${employee.avatar}`
        : null;
    const employeeName = employee.user?.name || 'Employee';
    const skills       = normaliseSkills(employee.skills);
    const duration     = computeDuration(employee.contract_start_date);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Employee: ${employeeName}`} />

            <style>{`
                @keyframes empFadeUp {
                    from { opacity: 0; transform: translateY(14px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
            `}</style>

            {/* ── Page shell ── */}
            <div className="px-4 sm:px-6 lg:px-8 py-5 max-w-7xl mx-auto space-y-5">

                <div
					className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3"
					style={{ animation: 'empFadeUp 0.4s ease both', animationDelay: '0ms' }}
				>
					{/* Back button – always left */}
					<button
						onClick={() => router.get('/employees')}
						className="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#1d4791] transition-colors group"
					>
						<ArrowLeft className="h-3.5 w-3.5 group-hover:-translate-x-0.5 transition-transform" />
						Back to list
					</button>

					{/* Action buttons – pushed to the right with ml-auto */}
					<div className="flex items-center gap-2 ml-auto sm:ml-0">
						<Link href={EmployeeController.edit(employee.slug_emp).url}>
							<Button
								variant="outline" size="sm"
								className="gap-1.5 h-8 text-xs border-slate-200 text-slate-700 hover:bg-[#1d4791] hover:text-white hover:border-[#1d4791] transition-all"
							>
								<Edit className="h-3.5 w-3.5" /> Edit
							</Button>
						</Link>

						<Dialog open={isDeleteOpen} onOpenChange={setIsDeleteOpen}>
							<DialogTrigger asChild>
								<Button
									variant="outline" size="sm"
									className="gap-1.5 h-8 text-xs border-[#d85e39]/30 text-[#d85e39] bg-[#d85e39]/5 hover:bg-[#d85e39] hover:text-white transition-all"
								>
									<Trash2 className="h-3.5 w-3.5" /> Delete
								</Button>
							</DialogTrigger>
							<DialogContent className="rounded-xl shadow-xl">
								<DialogHeader>
									<DialogTitle className="text-slate-900">Confirm deletion</DialogTitle>
									<DialogDescription className="text-slate-500">
										Are you sure you want to delete{' '}
										<span className="font-semibold text-slate-700">{employeeName}</span>?
										This action cannot be undone.
									</DialogDescription>
								</DialogHeader>
								<DialogFooter className="gap-2 sm:gap-0">
									<Button
										variant="outline"
										onClick={() => setIsDeleteOpen(false)}
										disabled={isDeleting}
										className="border-slate-200"
									>
										Cancel
									</Button>
									<Button
										onClick={handleDelete} disabled={isDeleting}
										className="bg-[#d85e39] hover:bg-[#d85e39]/90 text-white shadow-sm shadow-[#d85e39]/20"
									>
										{isDeleting ? 'Deleting…' : 'Delete'}
									</Button>
								</DialogFooter>
							</DialogContent>
						</Dialog>
					</div>
				</div>

                {/* ══ 2. PROFILE HERO (standalone, no outer card) ═══════════ */}
                <div
                    className="rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-white"
                    style={{ animation: 'empFadeUp 0.4s ease both', animationDelay: '50ms' }}
                >
                    {/* Navy identity bar */}
                    <div className="bg-[#1d4791] px-5 py-3.5 flex items-center justify-between gap-3">
                        <div className="flex items-center gap-2 min-w-0">
                            <User className="h-3.5 w-3.5 text-white/70 flex-shrink-0" />
                            <span className="text-[10px] font-bold tracking-widest uppercase text-white truncate">
                                Employee Profile
                            </span>
                        </div>
                        <StatusBadge status={employee.employee_status} />
                    </div>

                    {/* Avatar + identity body */}
                    <div className="p-5 sm:p-6 bg-slate-50/40">
                        <div className="flex flex-col sm:flex-row items-center sm:items-start gap-5">

                            {/* Avatar */}
                            <div className="flex-shrink-0">
                                {avatarUrl ? (
                                    <img
                                        src={avatarUrl}
                                        alt={employeeName}
                                        className="h-20 w-20 sm:h-24 sm:w-24 rounded-full object-cover border-4 border-white shadow-md"
                                        onError={e => {
                                            e.currentTarget.style.display = 'none';
                                            (e.currentTarget.nextElementSibling as HTMLElement | null)?.classList.remove('hidden');
                                        }}
                                    />
                                ) : null}
                                <div
                                    className={`h-20 w-20 sm:h-24 sm:w-24 rounded-full flex items-center justify-center text-2xl font-bold border-4 border-white shadow-md ${avatarUrl ? 'hidden' : ''}`}
                                    style={getAvatarBg(employeeName)}
                                >
                                    {getInitials(employeeName)}
                                </div>
                            </div>

                            {/* Name + meta */}
                            <div className="flex-1 min-w-0 text-center sm:text-left space-y-2">
                                <div>
                                    <h1 className="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                        {employeeName}
                                    </h1>
                                    <div className="flex items-center justify-center sm:justify-start flex-wrap gap-2 mt-1.5">
                                        <span className="text-xs text-slate-400 font-mono">#{employee.employee_number}</span>
                                        <span className="text-slate-200">·</span>
                                        <FreqBadge frequency={employee.pay_frequency} />
                                        {employee.position?.pos_name && (
                                            <>
                                                <span className="text-slate-200">·</span>
                                                <span className={`text-xs font-medium ${employee.position.deleted_at ? 'text-slate-300 line-through' : 'text-slate-500'}`}>
                                                    {employee.position.pos_name}
                                                </span>
                                            </>
                                        )}
                                    </div>
                                </div>

                                {/* Quick-contact row */}
                                <div className="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-1.5 pt-0.5">
                                    {employee.user?.email && (
                                        <a
                                            href={`mailto:${employee.user.email}`}
                                            className="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#1d4791] transition-colors"
                                        >
                                            <Mail className="h-3.5 w-3.5 text-slate-400" />
                                            {employee.user.email}
                                        </a>
                                    )}
                                    {employee.emergency_contact_number && (
                                        <a
                                            href={`tel:${employee.emergency_contact_number}`}
                                            className="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-[#1d4791] transition-colors"
                                        >
                                            <Phone className="h-3.5 w-3.5 text-slate-400" />
                                            {employee.emergency_contact_number}
                                        </a>
                                    )}
                                    {employee.branch?.branch_name && (
                                        <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                            <MapPin className="h-3.5 w-3.5 text-slate-400" />
                                            {employee.branch.branch_name}
                                        </span>
                                    )}
                                </div>
                            </div>

                            {/* Duration pill — top-right on desktop */}
                            {duration !== '—' && (
                                <div className="flex-shrink-0 hidden sm:flex flex-col items-end gap-1">
                                    <span className="text-[10px] text-slate-400 tracking-wide">Tenure</span>
                                    <span className="text-sm font-bold text-[#1d4791]">{duration}</span>
                                </div>
                            )}
                        </div>

                        {/* Duration — mobile only, below identity */}
                        {duration !== '—' && (
                            <div className="mt-3 pt-3 border-t border-slate-100 flex items-center justify-center sm:hidden gap-1.5">
                                <span className="text-[10px] text-slate-400">Tenure:</span>
                                <span className="text-xs font-bold text-[#1d4791]">{duration}</span>
                            </div>
                        )}
                    </div>
                </div>

                {/* ══ 3. RESPONSIVE MULTI‑COLUMN GRID (Skills + all sections) ══ */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-stretch">
                    
                    {/* Skills & Qualifications card */}
                    <div
                        className="rounded-xl overflow-hidden border border-slate-200 bg-white shadow-sm flex flex-col"
                        style={{ animation: 'empFadeUp 0.4s ease both', animationDelay: '100ms' }}
                    >
                        <div className="flex items-center gap-2 px-4 py-2.5 bg-[#1d4791]">
                            <Award className="h-3.5 w-3.5 text-white/75" />
                            <h3 className="text-[10px] font-bold tracking-widest uppercase text-white">Skills &amp; Qualifications</h3>
                        </div>
                        <div className="p-4 flex-1">
                            <div className="space-y-3">
                                <div>
                                    <p className="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1.5">Skills</p>
                                    <SkillChips skills={skills} />
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1.5">Certificate</p>
                                    <p className={`text-xs font-medium ${employee.certificate ? 'text-slate-800' : 'text-slate-300 italic'}`}>
                                        {employee.certificate || '—'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Personal Information */}
                    <Section icon={User} title="Personal Information" delay={140}>
                        <InfoRow label="Employee Code"   value={`#${employee.emp_code}`} />
                        <InfoRow label="Employee Number" value={employee.employee_number} />
                        <InfoRow label="Gender"          value={employee.gender} />
                        <InfoRow
                            label="Age"
                            value={employee.age !== undefined && employee.age !== null && employee.age !== '' ? `${employee.age} yrs` : null}
                        />
                        <InfoRow label="Date of Birth"   value={formatDate(employee.dob)} />
                    </Section>

                    {/* Contact Information */}
                    <Section icon={Phone} title="Contact Information" delay={160}>
                        <InfoRow
                            label="Emergency Number"
                            value={
                                employee.emergency_contact_number
                                    ? <a href={`tel:${employee.emergency_contact_number}`} className="text-[#1d4791] hover:underline">{employee.emergency_contact_number}</a>
                                    : null
                            }
                            isComponent
                        />
                        <InfoRow label="Contact Person" value={employee.contact_person} />
                        <InfoRow
                            label="Contact Number"
                            value={
                                employee.contact_person_number
                                    ? <a href={`tel:${employee.contact_person_number}`} className="text-[#1d4791] hover:underline">{employee.contact_person_number}</a>
                                    : null
                            }
                            isComponent
                        />
                    </Section>

                    {/* Family & Education */}
                    <Section icon={BookOpen} title="Family &amp; Education" delay={180}>
                        <InfoRow label="Mother's Name"           value={employee.mother_name} />
                        <InfoRow label="Father's Name"           value={employee.father_name} />
                        <InfoRow label="Educational Attainment"  value={employee.educ_attainment} />
                    </Section>

                    {/* Address Information */}
                    <Section icon={Home} title="Address Information" delay={200}>
                        <InfoBlock label="Permanent Address" value={employee.permanent_address} />
                        <InfoBlock label="Present Address"   value={employee.present_address} />
                    </Section>

                    {/* Government Numbers */}
                    <Section icon={Shield} title="Government Numbers" delay={220}>
                        <InfoRow label="SSS Number"     value={employee.sss_number} />
                        <InfoRow label="Pag-IBIG MID"   value={employee.pagibig_number} />
                        <InfoRow label="PhilHealth PIN"  value={employee.philhealth_number} />
                    </Section>

                    {/* Work Location */}
                    <Section icon={MapPin} title="Work Location" delay={240}>
                        <InfoRow label="Branch"         value={employee.branch?.branch_name} />
                        <InfoRow label="Branch Address" value={employee.branch?.branch_address} />
                        <InfoRow label="Site"           value={employee.sites?.site_name} />
                    </Section>

                    {/* Position & Pay */}
                    <Section icon={Briefcase} title="Position &amp; Pay" delay={260}>
                        <InfoRow
                            label="Position"
                            value={
                                employee.position?.pos_name
                                    ? employee.position.deleted_at
                                        ? <span className="text-slate-300 line-through">{employee.position.pos_name}</span>
                                        : employee.position.pos_name
                                    : null
                            }
                        />
                        <InfoRow
                            label="Pay Frequency"
                            value={<FreqBadge frequency={employee.pay_frequency} />}
                            isComponent
                        />
                    </Section>

                    {/* Contract Period */}
                    <Section icon={Calendar} title="Contract Period" delay={280}>
                        <InfoRow label="Start Date" value={formatDate(employee.contract_start_date)} />
                        <InfoRow label="End Date"   value={formatDate(employee.contract_end_date)} />
                        <div className="flex items-center justify-between pt-2 mt-1 border-t border-slate-100">
                            <dt className="text-[11px] text-slate-400">Duration</dt>
                            <dd className="text-xs font-bold text-[#1d4791]">{duration}</dd>
                        </div>
                    </Section>
                </div>
            </div>
        </AppLayout>
    );
}