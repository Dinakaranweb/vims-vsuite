@extends('frontend.frontend_master')

@section('content')
@php $user = Auth::user(); @endphp

<div id="app">
<div class="main-wrapper main-wrapper-1">

    @if($user->role === 'Staff')
        @include('frontend.staff.body.header')
        @include('frontend.staff.body.sidebar')
    @elseif($user->role === 'SuperAdmin')
        @include('frontend.superadmin.body.header')
        @include('frontend.superadmin.body.sidebar')
    @else
        @include('frontend.admin.body.header')
        @include('frontend.admin.body.sidebar')
    @endif

    <div class="main-content">
    <section class="section">

        {{-- ── Page Header ─────────────────────────────────────────────── --}}
        <div class="fc-hero">
            <div class="fc-hero-inner">
                <div>
                    <div class="fc-hero-tag">Document Approval System</div>
                    <h1 class="fc-hero-title">Approval Flow — Visual Guide</h1>
                    <p class="fc-hero-sub">
                        Every document follows a structured approval chain based on department division,
                        payment involvement, and amount. This chart shows every possible path a document can take.
                    </p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-12" style="gap:12px">
                    <div class="fc-legend-pills">
                        <span class="fc-pill fc-pill-start">Start / End</span>
                        <span class="fc-pill fc-pill-process">Process Step</span>
                        <span class="fc-pill fc-pill-decision">Decision</span>
                        <span class="fc-pill fc-pill-finance">Finance Step</span>
                        <span class="fc-pill fc-pill-done">Complete</span>
                    </div>
                    <button id="fc-pdf-btn" class="fc-download-btn" onclick="downloadFlowchartPDF()" title="Download as PDF">
                        <i id="fc-pdf-icon" class="fas fa-file-pdf"></i>
                        <span id="fc-pdf-label">Download PDF</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════
             FLOWCHART
        ══════════════════════════════════════════════════════════════ --}}
        <div class="fc-wrap">
        <div class="fc-canvas">

            {{-- ── ROW 1: Start ──────────────────────────────────────── --}}
            <div class="fc-row">
                <div class="fc-node fc-node-start">
                    <i class="fas fa-play-circle"></i>
                    <strong>Document Created</strong>
                    <span>HOD / Department Staff submits a new document request</span>
                </div>
            </div>
            <div class="fc-arrow-down"></div>

            {{-- ── ROW 2: Initial approver ────────────────────────────── --}}
            <div class="fc-row">
                <div class="fc-node fc-node-process">
                    <i class="fas fa-user-check"></i>
                    <strong>Select Initial Approver</strong>
                    <span>Creator picks the first approver: <em>Medical Director</em> or <em>General Manager</em></span>
                </div>
            </div>
            <div class="fc-arrow-down"></div>

            {{-- ── ROW 3: Division decision ────────────────────────────── --}}
            <div class="fc-row">
                <div class="fc-node fc-node-decision">
                    <i class="fas fa-code-branch"></i>
                    <strong>Clinical Division?</strong>
                    <span>Based on creator's department division</span>
                </div>
            </div>

            {{-- ── Division branch ────────────────────────────────────── --}}
            <div class="fc-branch-row">

                <div class="fc-branch fc-branch-left">
                    <div class="fc-branch-label fc-label-yes">Yes — Clinical</div>
                    <div class="fc-branch-arrow"></div>
                    <div class="fc-node fc-node-process fc-node-sm">
                        <i class="fas fa-user-md"></i>
                        <strong>Medical Director</strong>
                        <span>Reviews &amp; approves first</span>
                    </div>
                    <div class="fc-branch-arrow-down"></div>
                    <div class="fc-node fc-node-process fc-node-sm">
                        <i class="fas fa-briefcase-medical"></i>
                        <strong>General Manager</strong>
                        <span>Reviews &amp; approves second</span>
                    </div>
                </div>

                <div class="fc-branch-center-line"></div>

                <div class="fc-branch fc-branch-right">
                    <div class="fc-branch-label fc-label-no">No — Non-Clinical</div>
                    <div class="fc-branch-arrow"></div>
                    <div class="fc-node fc-node-process fc-node-sm">
                        <i class="fas fa-briefcase"></i>
                        <strong>General Manager</strong>
                        <span>Reviews &amp; approves first</span>
                    </div>
                    <div class="fc-branch-arrow-down"></div>
                    <div class="fc-node fc-node-process fc-node-sm">
                        <i class="fas fa-user-md"></i>
                        <strong>Medical Director</strong>
                        <span>Reviews &amp; approves second</span>
                    </div>
                </div>

            </div>

            <div class="fc-merge-row">
                <div class="fc-merge-line-left"></div>
                <div class="fc-merge-arrow-down"></div>
                <div class="fc-merge-line-right"></div>
            </div>

            {{-- ── ROW 4: Payment decision ─────────────────────────────── --}}
            <div class="fc-row">
                <div class="fc-node fc-node-decision">
                    <i class="fas fa-coins"></i>
                    <strong>Payment Involved?</strong>
                    <span>Is this document requesting a payment or purchase?</span>
                </div>
            </div>

            {{-- ── Payment No branch: complete ─────────────────────────── --}}
            <div class="fc-side-exit">
                <div class="fc-side-exit-line"></div>
                <div class="fc-side-exit-label fc-label-no">No</div>
                <div class="fc-node fc-node-done fc-node-sm">
                    <i class="fas fa-flag-checkered"></i>
                    <strong>Document Completed</strong>
                    <span>Fully approved. Returned to creator.</span>
                </div>
            </div>

            <div class="fc-arrow-down fc-label-yes-inline">Yes — Payment Required</div>

            {{-- ── ROW 5: Amount decision ──────────────────────────────── --}}
            <div class="fc-row">
                <div class="fc-node fc-node-decision">
                    <i class="fas fa-rupee-sign"></i>
                    <strong>Amount &gt; ₹2,00,000?</strong>
                    <span>High-value documents need Chairman approval</span>
                </div>
            </div>

            {{-- ── Amount branches ─────────────────────────────────────── --}}
            <div class="fc-branch-row fc-branch-row-wide">

                {{-- ─── LEFT: Amount > 2L ─────────────────────────────── --}}
                <div class="fc-branch fc-branch-left">
                    <div class="fc-branch-label fc-label-yes">Yes — High Value (&gt; ₹2L)</div>
                    <div class="fc-branch-arrow"></div>

                    <div class="fc-node fc-node-process fc-node-sm">
                        <i class="fas fa-landmark"></i>
                        <strong>STB Office</strong>
                        <span>Reviews &amp; acknowledges document</span>
                    </div>
                    <div class="fc-branch-arrow-down"></div>

                    <div class="fc-node fc-node-special fc-node-sm">
                        <i class="fas fa-user-tie"></i>
                        <strong>Chairman</strong>
                        <span>Final high-level approval. May directly select Finance Head.</span>
                    </div>
                    <div class="fc-branch-arrow-down"></div>

                    {{-- Purchase sub-decision --}}
                    <div class="fc-node fc-node-decision fc-node-sm">
                        <i class="fas fa-shopping-cart"></i>
                        <strong>Is it a Purchase?</strong>
                    </div>

                    <div class="fc-sub-branch">
                        <div class="fc-sub-left">
                            <div class="fc-branch-label fc-label-yes" style="font-size:10px">Yes</div>
                            <div class="fc-node fc-node-finance fc-node-xs">
                                <i class="fas fa-boxes"></i>
                                <strong>Purchase Head Chennai</strong>
                                <span>Reviews purchase order</span>
                            </div>
                        </div>
                        <div class="fc-sub-right">
                            <div class="fc-branch-label fc-label-no" style="font-size:10px">No</div>
                            <div class="fc-vline-skip"></div>
                        </div>
                    </div>

                    <div class="fc-branch-arrow-down"></div>

                    <div class="fc-node fc-node-finance fc-node-sm">
                        <i class="fas fa-user-secret"></i>
                        <strong>PA to Chairman</strong>
                        <span>Selects the appropriate Finance Head location</span>
                    </div>
                    <div class="fc-branch-arrow-down"></div>

                    <div class="fc-node fc-node-finance fc-node-sm fc-node-highlight">
                        <i class="fas fa-university"></i>
                        <strong>Finance Head</strong>
                        <span>Salem / Chennai / Karaikal / Pondy<br><em>Selected by PA to Chairman or directly by Chairman</em></span>
                    </div>
                </div>

                <div class="fc-branch-center-line"></div>

                {{-- ─── RIGHT: Amount ≤ 2L ─────────────────────────────── --}}
                <div class="fc-branch fc-branch-right">
                    <div class="fc-branch-label fc-label-no">No — Low Value (≤ ₹2L)</div>
                    <div class="fc-branch-arrow"></div>

                    <div class="fc-node fc-node-decision fc-node-sm">
                        <i class="fas fa-shopping-cart"></i>
                        <strong>Is it a Purchase?</strong>
                    </div>

                    <div class="fc-sub-branch">
                        <div class="fc-sub-left">
                            <div class="fc-branch-label fc-label-yes" style="font-size:10px">Yes</div>
                            <div class="fc-node fc-node-finance fc-node-xs">
                                <i class="fas fa-boxes"></i>
                                <strong>Purchase Head</strong>
                                <span>Reviews purchase order</span>
                            </div>
                        </div>
                        <div class="fc-sub-right">
                            <div class="fc-branch-label fc-label-no" style="font-size:10px">No</div>
                            <div class="fc-vline-skip"></div>
                        </div>
                    </div>

                    <div class="fc-branch-arrow-down"></div>

                    <div class="fc-node fc-node-finance fc-node-sm fc-node-highlight">
                        <i class="fas fa-university"></i>
                        <strong>Finance Head Salem</strong>
                        <span>Direct finance processing — no PA step needed</span>
                    </div>
                </div>

            </div>

            {{-- ── Merge back after Finance Head ───────────────────────── --}}
            <div class="fc-merge-row">
                <div class="fc-merge-line-left"></div>
                <div class="fc-merge-arrow-down"></div>
                <div class="fc-merge-line-right"></div>
            </div>

            {{-- ── ROW 6: Finance Head actions ─────────────────────────── --}}
            <div class="fc-row">
                <div class="fc-node fc-node-decision">
                    <i class="fas fa-hand-holding-usd"></i>
                    <strong>Finance Head Action</strong>
                    <span>What does the Finance Head do with this document?</span>
                </div>
            </div>

            {{-- ── Finance action branches ─────────────────────────────── --}}
            <div class="fc-branch-row fc-branch-row-triple">

                <div class="fc-branch fc-branch-t">
                    <div class="fc-branch-label fc-label-a">Full Payment</div>
                    <div class="fc-branch-arrow"></div>
                    <div class="fc-node fc-node-finance fc-node-sm">
                        <i class="fas fa-check-double"></i>
                        <strong>Payment Processed</strong>
                        <span>Records amount, date, reference. Workflow advances.</span>
                    </div>
                    <div class="fc-branch-arrow-down"></div>
                    <div class="fc-node fc-node-done fc-node-sm">
                        <i class="fas fa-flag-checkered"></i>
                        <strong>Completed</strong>
                    </div>
                </div>

                <div class="fc-branch fc-branch-t">
                    <div class="fc-branch-label fc-label-b">Partial Payment</div>
                    <div class="fc-branch-arrow"></div>
                    <div class="fc-node fc-node-finance fc-node-sm">
                        <i class="fas fa-percentage"></i>
                        <strong>Advance Recorded</strong>
                        <span>Partial amount noted. Document stays at Finance Head for balance.</span>
                    </div>
                    <div class="fc-branch-arrow-down fc-loop-arrow">
                        <span class="fc-loop-label">Awaits full payment</span>
                    </div>
                    <div class="fc-node fc-node-finance fc-node-sm" style="border: 2px dashed #e67e22;">
                        <i class="fas fa-hourglass-half"></i>
                        <strong>Finance Head (continued)</strong>
                        <span>Document remains here until full payment is made</span>
                    </div>
                </div>

                <div class="fc-branch fc-branch-t">
                    <div class="fc-branch-label fc-label-c">Forward</div>
                    <div class="fc-branch-arrow"></div>
                    <div class="fc-node fc-node-finance fc-node-sm">
                        <i class="fas fa-exchange-alt"></i>
                        <strong>Finance Head Changed</strong>
                        <span>Document forwarded to another Finance location (Salem/Chennai/Karaikal/Pondy)</span>
                    </div>
                    <div class="fc-branch-arrow-down fc-loop-arrow">
                        <span class="fc-loop-label">Re-enters at Finance Head</span>
                    </div>
                    <div class="fc-node fc-node-finance fc-node-sm" style="border:2px dashed #8e44ad;">
                        <i class="fas fa-university"></i>
                        <strong>New Finance Head</strong>
                        <span>Receives and processes</span>
                    </div>
                </div>

            </div>

            {{-- ── Rejection path note ──────────────────────────────────── --}}
            <div class="fc-row" style="margin-top:32px">
                <div class="fc-reject-note">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Rejection at any step</strong>
                        Any approver (Medical Director, General Manager, STB Office, Chairman, Finance Head)
                        can <strong>Reject</strong> the document. This immediately terminates the workflow
                        and marks the document as <span class="fc-status-reject">Rejected</span>.
                        The creator is notified.
                    </div>
                </div>
            </div>

        </div>{{-- /.fc-canvas --}}
        </div>{{-- /.fc-wrap --}}

        {{-- ══════════════════════════════════════════════════════════════
             STEP-BY-STEP EXPLANATION TABLE
        ══════════════════════════════════════════════════════════════ --}}
        <div class="fc-explain-section">

            <div class="fc-explain-title">
                <i class="fas fa-list-alt"></i> Step-by-Step Explanation
            </div>

            <div class="row">

                {{-- Path A --}}
                <div class="col-xl-6 mb-4">
                    <div class="fc-path-card fc-path-green">
                        <div class="fc-path-hdr">
                            <span class="fc-path-num">A</span>
                            <span>Non-Payment Document</span>
                        </div>
                        <ol class="fc-path-steps">
                            <li>Creator submits document (no payment)</li>
                            <li>Routes to Medical Director or General Manager (division-based order)</li>
                            <li>Both review &amp; approve in sequence</li>
                            <li class="fc-step-done">Document Completed — returned to creator</li>
                        </ol>
                    </div>
                </div>

                {{-- Path B --}}
                <div class="col-xl-6 mb-4">
                    <div class="fc-path-card fc-path-blue">
                        <div class="fc-path-hdr">
                            <span class="fc-path-num">B</span>
                            <span>Payment ≤ ₹2,00,000 — No Purchase</span>
                        </div>
                        <ol class="fc-path-steps">
                            <li>Creator submits with payment (low value)</li>
                            <li>Medical Director → General Manager (or reverse, division-based)</li>
                            <li>Forwarded directly to <strong>Finance Head Salem</strong></li>
                            <li>Finance Head processes payment (Full or Partial)</li>
                            <li class="fc-step-done">Document Completed</li>
                        </ol>
                    </div>
                </div>

                {{-- Path C --}}
                <div class="col-xl-6 mb-4">
                    <div class="fc-path-card fc-path-orange">
                        <div class="fc-path-hdr">
                            <span class="fc-path-num">C</span>
                            <span>Payment ≤ ₹2,00,000 — With Purchase</span>
                        </div>
                        <ol class="fc-path-steps">
                            <li>Creator submits with payment + purchase flag</li>
                            <li>Medical Director → General Manager (or reverse)</li>
                            <li><strong>Purchase Head</strong> reviews the purchase order</li>
                            <li>Forwarded to <strong>Finance Head Salem</strong></li>
                            <li>Finance Head processes payment</li>
                            <li class="fc-step-done">Document Completed</li>
                        </ol>
                    </div>
                </div>

                {{-- Path D --}}
                <div class="col-xl-6 mb-4">
                    <div class="fc-path-card fc-path-purple">
                        <div class="fc-path-hdr">
                            <span class="fc-path-num">D</span>
                            <span>Payment &gt; ₹2,00,000 — No Purchase</span>
                        </div>
                        <ol class="fc-path-steps">
                            <li>Creator submits with high-value payment</li>
                            <li>Medical Director → General Manager (or reverse)</li>
                            <li><strong>STB Office</strong> acknowledges and reviews</li>
                            <li><strong>Chairman</strong> gives final approval</li>
                            <li><strong>PA to Chairman</strong> selects the Finance Head location</li>
                            <li>Selected <strong>Finance Head</strong> (Salem/Chennai/Karaikal/Pondy) processes</li>
                            <li class="fc-step-done">Document Completed</li>
                        </ol>
                    </div>
                </div>

                {{-- Path E --}}
                <div class="col-xl-6 mb-4">
                    <div class="fc-path-card fc-path-red">
                        <div class="fc-path-hdr">
                            <span class="fc-path-num">E</span>
                            <span>Payment &gt; ₹2,00,000 — With Purchase</span>
                        </div>
                        <ol class="fc-path-steps">
                            <li>Creator submits with high-value payment + purchase</li>
                            <li>Medical Director → General Manager (or reverse)</li>
                            <li><strong>STB Office</strong> acknowledges</li>
                            <li><strong>Chairman</strong> approves</li>
                            <li><strong>Purchase Head Chennai</strong> reviews the purchase order</li>
                            <li><strong>PA to Chairman</strong> selects Finance Head location</li>
                            <li>Selected <strong>Finance Head</strong> processes payment</li>
                            <li class="fc-step-done">Document Completed</li>
                        </ol>
                    </div>
                </div>

                {{-- Path F — Chairman shortcut --}}
                <div class="col-xl-6 mb-4">
                    <div class="fc-path-card fc-path-teal">
                        <div class="fc-path-hdr">
                            <span class="fc-path-num">F</span>
                            <span>Chairman Direct Finance Head Selection</span>
                        </div>
                        <ol class="fc-path-steps">
                            <li>Applies to <strong>Path D &amp; E</strong> only</li>
                            <li>At the Chairman approval step, Chairman can <em>directly choose</em> a Finance Head</li>
                            <li>This replaces the PA to Chairman step in the sequence</li>
                            <li>Document jumps straight to the chosen Finance Head</li>
                            <li class="fc-step-done">PA step skipped — faster processing</li>
                        </ol>
                    </div>
                </div>

            </div>

            {{-- ── Actions table ──────────────────────────────────────── --}}
            <div class="fc-actions-title">
                <i class="fas fa-bolt"></i> Available Actions at Each Step
            </div>

            <div class="table-responsive">
                <table class="table fc-actions-table">
                    <thead>
                        <tr>
                            <th>Approver / Role</th>
                            <th>Can Approve</th>
                            <th>Can Reject</th>
                            <th>Can Hold</th>
                            <th>Can Forward</th>
                            <th>Special Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fc-role-badge fc-role-hod">HOD / Creator</span></td>
                            <td class="text-muted">—</td>
                            <td class="text-muted">—</td>
                            <td class="text-muted">—</td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td>Submit, Retract, Comment, Download PDF</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-md">Medical Director</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td>Approve in Principle, Noted, Discussion, Comment</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-gm">General Manager</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td>Approve in Principle, Noted, Discussion, Comment</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-stb">STB Office</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td>Acknowledge, Comment</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-chair">Chairman</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><strong>Direct Finance Head selection</strong>, Recommended/Sanctioned amounts, API approval</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-purchase">Purchase Head</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td>Purchase Order Review, Comment</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-pa">PA to Chairman</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><strong>Select Finance Head location</strong> (Salem/Chennai/Karaikal/Pondy)</td>
                        </tr>
                        <tr>
                            <td><span class="fc-role-badge fc-role-finance">Finance Head</span></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><i class="fas fa-check fc-yes"></i></td>
                            <td><strong>Full Payment</strong> / <strong>Partial (Advance)</strong> / <strong>Forward to another Finance location</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- ── Status legend ──────────────────────────────────────── --}}
            <div class="fc-status-section">
                <div class="fc-actions-title"><i class="fas fa-info-circle"></i> Document Status Values</div>
                <div class="fc-status-grid">
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#fef9e7;color:#d4a017;border:1px solid #f0c040">Draft</span>Not yet submitted</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#eaf6fb;color:#2471a3;border:1px solid #85c1e9">Sent to X</span>Awaiting X's action</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#fef5e7;color:#ca6f1e;border:1px solid #f0b27a">Hold</span>Paused by approver</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#fdedec;color:#c0392b;border:1px solid #f1948a">Rejected</span>Terminated — rejected</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#e8f8f5;color:#1e8449;border:1px solid #82e0aa">Completed</span>All approvals done</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#f4ecf7;color:#7d3c98;border:1px solid #c39bd3">Closed</span>Process closed by creator</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#f0f3fa;color:#2c3e7a;border:1px solid #a9b4d4">Discussion</span>Sent for discussion</div>
                    <div class="fc-status-item"><span class="fc-s-badge" style="background:#fdfefe;color:#616a6b;border:1px solid #bdc3c7">Retracted</span>Pulled back by creator</div>
                </div>
            </div>

        </div>{{-- /.fc-explain-section --}}

    </section>
    </div>
</div>
</div>

{{-- ─────────────────────────────────────────────────────────────────────────
     STYLES
───────────────────────────────────────────────────────────────────────── --}}
<style>
/* ── Hero ────────────────────────────────────────────────────────────── */
.fc-hero {
    background: linear-gradient(135deg,#1c1c3a 0%,#2c3e7a 55%,#1a3a52 100%);
    border-radius: 16px;
    padding: 32px 36px;
    margin-bottom: 32px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(28,28,58,.3);
}
.fc-hero::after {
    content:''; position:absolute; top:-80px; right:-60px;
    width:260px; height:260px; border-radius:50%;
    background:rgba(255,255,255,.04); pointer-events:none;
}
.fc-hero-inner { position:relative; z-index:1; }
.fc-hero-tag   { font-size:11px; letter-spacing:2px; text-transform:uppercase; opacity:.6; margin-bottom:8px; }
.fc-hero-title { font-size:26px; font-weight:800; margin:0 0 10px; line-height:1.2; }
.fc-hero-sub   { font-size:14px; opacity:.75; max-width:640px; margin:0 0 20px; line-height:1.6; }
.fc-legend-pills { display:flex; flex-wrap:wrap; gap:8px; }
.fc-pill { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.fc-pill-start    { background:rgba(39,174,96,.25); color:#7dcea0; border:1px solid rgba(39,174,96,.4); }
.fc-pill-process  { background:rgba(52,152,219,.25); color:#85c1e9; border:1px solid rgba(52,152,219,.4); }
.fc-pill-decision { background:rgba(243,156,18,.25); color:#f8c471; border:1px solid rgba(243,156,18,.4); }
.fc-pill-finance  { background:rgba(155,89,182,.25); color:#d2b4de; border:1px solid rgba(155,89,182,.4); }
.fc-pill-done     { background:rgba(39,174,96,.25); color:#7dcea0; border:1px solid rgba(39,174,96,.4); }
.fc-download-btn {
    display:inline-flex; align-items:center; gap:7px;
    background:rgba(255,255,255,.15);
    color:#fff;
    border:1.5px solid rgba(255,255,255,.4);
    border-radius:8px;
    padding:7px 16px;
    font-size:13px; font-weight:700;
    text-decoration:none;
    transition:background .2s,border-color .2s;
    white-space:nowrap;
}
.fc-download-btn:hover { background:rgba(255,255,255,.25); border-color:rgba(255,255,255,.8); color:#fff; text-decoration:none; }
.fc-download-btn i { font-size:16px; color:#ff6b6b; }

/* ── Canvas wrapper ──────────────────────────────────────────────────── */
.fc-wrap   { overflow-x:auto; padding-bottom:12px; }
.fc-canvas { min-width:760px; max-width:1100px; margin:0 auto; padding:0 12px 32px; }

/* ── Shared node styles ──────────────────────────────────────────────── */
.fc-row { display:flex; justify-content:center; }
.fc-node {
    border-radius:12px;
    padding:14px 22px;
    text-align:center;
    position:relative;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:4px;
    min-width:220px;
    max-width:340px;
    box-shadow:0 3px 14px rgba(0,0,0,.09);
}
.fc-node i        { font-size:20px; margin-bottom:2px; }
.fc-node strong   { font-size:13.5px; font-weight:700; display:block; }
.fc-node span     { font-size:11.5px; color:#666; line-height:1.4; }
.fc-node.fc-node-sm  { min-width:180px; max-width:260px; padding:12px 16px; }
.fc-node.fc-node-xs  { min-width:140px; max-width:200px; padding:10px 14px; }
.fc-node.fc-node-xs strong { font-size:12px; }
.fc-node.fc-node-xs span   { font-size:10.5px; }

/* Node type colours */
.fc-node-start   { background:linear-gradient(135deg,#e8f8f0,#d5f5e3); border:2px solid #82e0aa; }
.fc-node-start i { color:#27ae60; }
.fc-node-process { background:#fff; border:2px solid #d5e8fb; }
.fc-node-process i { color:#3498db; }
.fc-node-decision { background:linear-gradient(135deg,#fef9e7,#fdebd0); border:2px solid #f0c040; }
.fc-node-decision i { color:#e67e22; }
.fc-node-special  { background:linear-gradient(135deg,#f0ebfa,#e4d6f7); border:2px solid #c39bd3; }
.fc-node-special i { color:#8e44ad; }
.fc-node-finance  { background:linear-gradient(135deg,#f4f0ff,#ede0ff); border:2px solid #b39ddb; }
.fc-node-finance i { color:#6c5ecf; }
.fc-node-finance.fc-node-highlight { border-color:#6c5ecf; box-shadow:0 0 0 3px rgba(108,94,207,.15); }
.fc-node-done     { background:linear-gradient(135deg,#e8f8f0,#d5f5e3); border:2px solid #27ae60; }
.fc-node-done i   { color:#27ae60; }

/* ── Arrows ──────────────────────────────────────────────────────────── */
.fc-arrow-down {
    width:2px; height:28px;
    background:#bdc3c7;
    margin:0 auto;
    position:relative;
}
.fc-arrow-down::after {
    content:'';
    position:absolute;
    bottom:-7px; left:-5px;
    border-left:6px solid transparent;
    border-right:6px solid transparent;
    border-top:8px solid #bdc3c7;
}
.fc-label-yes-inline {
    display:flex; align-items:center; justify-content:center;
    gap:8px;
    font-size:11px; font-weight:700; color:#27ae60;
    letter-spacing:.5px; text-transform:uppercase;
    height:28px;
    position:relative;
}
.fc-label-yes-inline::before {
    content:''; flex:1; max-width:80px; height:2px; background:#27ae60; opacity:.3;
}
.fc-label-yes-inline::after {
    content:''; position:absolute; bottom:-6px; left:50%; transform:translateX(-50%);
    border-left:6px solid transparent; border-right:6px solid transparent;
    border-top:8px solid #bdc3c7;
}

/* ── Division & Amount branch rows ──────────────────────────────────── */
.fc-branch-row {
    display:flex;
    align-items:stretch;
    justify-content:center;
    gap:0;
    margin:0 auto;
    max-width:820px;
    width:100%;
}
.fc-branch-row-wide  { max-width:960px; }
.fc-branch-row-triple { max-width:1060px; gap:0; }

.fc-branch {
    display:flex; flex-direction:column; align-items:center;
    flex:1; padding:0 10px;
    gap:6px;
}
.fc-branch-left  { border-right:1px dashed #dde; }
.fc-branch-right { border-left:1px dashed #dde; }
.fc-branch-t     { border-right:1px dashed #dde; }
.fc-branch-t:last-child { border-right:none; }

.fc-branch-label {
    font-size:11.5px; font-weight:700; text-transform:uppercase;
    letter-spacing:.6px; padding:3px 12px; border-radius:20px;
    margin-top:8px;
}
.fc-label-yes { background:#e8f8f0; color:#27ae60; }
.fc-label-no  { background:#fef3e4; color:#ca6f1e; }
.fc-label-a   { background:#ebf5fb; color:#2471a3; }
.fc-label-b   { background:#fef5e7; color:#ca6f1e; }
.fc-label-c   { background:#f5eef8; color:#7d3c98; }

.fc-branch-arrow {
    width:2px; height:20px; background:#bdc3c7;
    position:relative;
}
.fc-branch-arrow::after {
    content:''; position:absolute; bottom:-7px; left:-5px;
    border-left:6px solid transparent; border-right:6px solid transparent;
    border-top:8px solid #bdc3c7;
}
.fc-branch-arrow-down {
    width:2px; height:18px; background:#bdc3c7; position:relative;
}
.fc-branch-arrow-down::after {
    content:''; position:absolute; bottom:-7px; left:-5px;
    border-left:6px solid transparent; border-right:6px solid transparent;
    border-top:8px solid #bdc3c7;
}
.fc-branch-arrow-down.fc-loop-arrow { height:28px; }
.fc-loop-label {
    position:absolute; left:10px; top:50%;
    transform:translateY(-50%);
    font-size:10px; color:#e67e22; white-space:nowrap;
    background:#fff8f0; padding:1px 6px; border-radius:4px;
    border:1px solid #f0b27a;
}

.fc-branch-center-line {
    width:2px; background:#e5e5e5; align-self:stretch; flex-shrink:0;
}

/* ── Sub-branches (purchase decision) ───────────────────────────────── */
.fc-sub-branch {
    display:flex; gap:6px; align-items:flex-start; width:100%;
    padding:0 4px;
}
.fc-sub-left, .fc-sub-right {
    display:flex; flex-direction:column; align-items:center; flex:1; gap:4px;
}
.fc-vline-skip {
    width:2px; height:56px; background:#e5e5e5;
}

/* ── Merge row ───────────────────────────────────────────────────────── */
.fc-merge-row {
    display:flex; justify-content:center; align-items:flex-end;
    max-width:820px; margin:0 auto; height:24px;
}
.fc-merge-line-left {
    flex:1; border-bottom:2px solid #bdc3c7; border-left:2px solid #bdc3c7;
    border-radius:0 0 0 8px;
    height:20px;
}
.fc-merge-line-right {
    flex:1; border-bottom:2px solid #bdc3c7; border-right:2px solid #bdc3c7;
    border-radius:0 0 8px 0;
    height:20px;
}
.fc-merge-arrow-down {
    width:2px; height:24px; background:#bdc3c7;
    position:relative; flex-shrink:0;
}
.fc-merge-arrow-down::after {
    content:''; position:absolute; bottom:-7px; left:-5px;
    border-left:6px solid transparent; border-right:6px solid transparent;
    border-top:8px solid #bdc3c7;
}

/* ── Side exit (Payment No) ──────────────────────────────────────────── */
.fc-side-exit {
    display:flex; align-items:center; gap:12px;
    max-width:820px; margin:0 auto 0;
    padding:8px 0;
    justify-content:flex-end;
}
.fc-side-exit-line {
    flex:1; height:2px; background:#bdc3c7;
    position:relative;
}
.fc-side-exit-label {
    font-size:11px; font-weight:700; padding:2px 8px;
    border-radius:10px; white-space:nowrap;
}

/* ── Reject note ─────────────────────────────────────────────────────── */
.fc-reject-note {
    display:flex; align-items:flex-start; gap:14px;
    background:#fdedec; border:1.5px solid #f1948a;
    border-radius:12px; padding:16px 20px;
    max-width:720px; margin:0 auto;
}
.fc-reject-note i { color:#c0392b; font-size:20px; margin-top:2px; flex-shrink:0; }
.fc-reject-note   { font-size:13.5px; color:#6e2c2c; line-height:1.6; }
.fc-status-reject { background:#c0392b; color:#fff; padding:1px 8px; border-radius:4px; font-size:12px; font-weight:700; }

/* ── Explanation section ─────────────────────────────────────────────── */
.fc-explain-section { margin-top:40px; }
.fc-explain-title, .fc-actions-title {
    font-size:15px; font-weight:800;
    color:#2c3e50; margin-bottom:18px;
    display:flex; align-items:center; gap:10px;
    padding-bottom:8px;
    border-bottom:2px solid #ecf0f1;
}
.fc-explain-title i, .fc-actions-title i { color:#6c5ecf; }
.fc-actions-title { margin-top:32px; }

/* ── Path cards ──────────────────────────────────────────────────────── */
.fc-path-card {
    border-radius:12px; padding:20px 22px;
    height:100%; box-shadow:0 2px 12px rgba(0,0,0,.07);
}
.fc-path-hdr {
    display:flex; align-items:center; gap:12px;
    margin-bottom:12px;
    font-size:14px; font-weight:700;
}
.fc-path-num {
    width:30px; height:30px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:800; color:#fff;
    flex-shrink:0;
}
.fc-path-steps { padding-left:18px; margin:0; }
.fc-path-steps li { margin-bottom:6px; font-size:13.5px; color:#555; line-height:1.5; }
.fc-step-done { font-weight:700 !important; }

.fc-path-green  { background:#f0faf4; border:1.5px solid #a9dfbf; }
.fc-path-green .fc-path-num { background:#27ae60; }
.fc-path-green .fc-step-done { color:#1e8449; }

.fc-path-blue   { background:#f0f8ff; border:1.5px solid #aed6f1; }
.fc-path-blue .fc-path-num { background:#2980b9; }
.fc-path-blue .fc-step-done { color:#1a5276; }

.fc-path-orange { background:#fef9f0; border:1.5px solid #f9c784; }
.fc-path-orange .fc-path-num { background:#e67e22; }
.fc-path-orange .fc-step-done { color:#a04000; }

.fc-path-purple { background:#f8f3ff; border:1.5px solid #d7bde2; }
.fc-path-purple .fc-path-num { background:#8e44ad; }
.fc-path-purple .fc-step-done { color:#6c3483; }

.fc-path-red    { background:#fdf3f3; border:1.5px solid #f1948a; }
.fc-path-red .fc-path-num { background:#e74c3c; }
.fc-path-red .fc-step-done { color:#922b21; }

.fc-path-teal   { background:#f0fbfa; border:1.5px solid #a2d9ce; }
.fc-path-teal .fc-path-num { background:#16a085; }
.fc-path-teal .fc-step-done { color:#0e6655; }

/* ── Actions table ───────────────────────────────────────────────────── */
.fc-actions-table thead th {
    background:#f8f9fc;
    font-size:11px; text-transform:uppercase; letter-spacing:.6px;
    color:#6c757d; font-weight:700;
    padding:11px 14px; border-top:none; border-bottom:2px solid #eee;
}
.fc-actions-table tbody td {
    padding:11px 14px; vertical-align:middle;
    border-bottom:1px solid #f4f4f4; font-size:13px;
}
.fc-actions-table tbody tr:last-child td { border-bottom:none; }
.fc-actions-table tbody tr:hover { background:#fafbff; }
.fc-yes { color:#27ae60; font-size:14px; }
.fc-role-badge { padding:3px 10px; border-radius:6px; font-size:11.5px; font-weight:700; white-space:nowrap; }
.fc-role-hod      { background:#eaf6fb; color:#2471a3; }
.fc-role-md       { background:#e8f8f5; color:#1a7a5e; }
.fc-role-gm       { background:#fef9e7; color:#9a7d0a; }
.fc-role-stb      { background:#f0f3fa; color:#2c3e7a; }
.fc-role-chair    { background:#f4ecf7; color:#7d3c98; }
.fc-role-purchase { background:#fef5e7; color:#ca6f1e; }
.fc-role-pa       { background:#fdf2f8; color:#a93226; }
.fc-role-finance  { background:#f0eeff; color:#5b2c8d; }

/* ── Status grid ─────────────────────────────────────────────────────── */
.fc-status-section { margin-top:28px; }
.fc-status-grid {
    display:flex; flex-wrap:wrap; gap:10px;
}
.fc-status-item {
    display:flex; align-items:center; gap:8px;
    font-size:13px; color:#555;
}
.fc-s-badge {
    padding:3px 10px; border-radius:6px; font-size:12px; font-weight:700;
    white-space:nowrap;
}

@media (max-width:768px) {
    .fc-branch-row { flex-direction:column; align-items:center; gap:8px; }
    .fc-branch      { border:none !important; width:100%; max-width:340px; }
    .fc-branch-center-line { width:100%; height:2px; }
    .fc-side-exit   { flex-direction:column; align-items:center; }
    .fc-merge-row   { display:none; }
    .fc-hero-title  { font-size:20px; }
}
</style>

{{-- ── Client-side PDF download (html2canvas + jsPDF, no server needed) ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function downloadFlowchartPDF() {
    const btn   = document.getElementById('fc-pdf-btn');
    const label = document.getElementById('fc-pdf-label');

    const icon  = document.getElementById('fc-pdf-icon');

    // Loading state
    btn.disabled = true;
    label.textContent = 'Generating…';
    btn.style.opacity = '0.7';
    icon.className = 'fas fa-spinner fa-spin';

    try {
        const { jsPDF } = window.jspdf;

        // Capture the entire section (hero + flowchart + explanation)
        const target = document.querySelector('.section');

        const canvas = await html2canvas(target, {
            scale: 2,                // 2× resolution for crisp text
            useCORS: true,
            backgroundColor: '#f5f6fa',
            logging: false,
            windowWidth: 1200        // fixed render width so layout is consistent
        });

        const imgData = canvas.toDataURL('image/png');

        // A4 landscape dimensions in mm
        const pageW = 297;
        const pageH = 210;
        const margin = 10;
        const usableW = pageW - margin * 2;

        // Work out how many A4 pages the content fills
        const pxPerMm  = canvas.width / usableW;
        const pageHeightPx = pageH * pxPerMm;
        const totalPages   = Math.ceil(canvas.height / pageHeightPx);

        const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

        for (let page = 0; page < totalPages; page++) {
            if (page > 0) pdf.addPage();

            // Slice the canvas vertically for this page
            const srcY  = page * pageHeightPx;
            const srcH  = Math.min(pageHeightPx, canvas.height - srcY);

            const slice = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = srcH;
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcH, 0, 0, canvas.width, srcH);

            const sliceData  = slice.toDataURL('image/png');
            const sliceHMm   = (srcH / pxPerMm);

            pdf.addImage(sliceData, 'PNG', margin, margin, usableW, sliceHMm);
        }

        const today = new Date();
        const stamp = today.getFullYear()
            + String(today.getMonth() + 1).padStart(2, '0')
            + String(today.getDate()).padStart(2, '0');

        pdf.save('Approval-Flow-Guide-' + stamp + '.pdf');

    } catch (err) {
        console.error('PDF generation failed:', err);
        alert('Could not generate PDF. Please try again.');
    } finally {
        btn.disabled = false;
        label.textContent = 'Download PDF';
        btn.style.opacity = '1';
        icon.className = 'fas fa-file-pdf';
    }
}
</script>

@endsection
