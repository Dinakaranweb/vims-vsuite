<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Document Approval Flow Guide</title>
<style type="text/css">
* { box-sizing: border-box; margin: 0; padding: 0; }
@page { size: A4 landscape; margin: 10mm 12mm; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #2c3e50; background: #fff; line-height: 1.4; }

/* ── Utilities ─── */
.text-center { text-align: center; }
.text-left   { text-align: left; }
.bold        { font-weight: bold; }
.small       { font-size: 9.5px; }
.muted       { color: #7f8c8d; }
.w100        { width: 100%; }

/* ── Header ─── */
.page-header { width: 100%; border-bottom: 3px solid #1c1c3a; padding-bottom: 8px; margin-bottom: 14px; }
.header-table { width: 100%; border-collapse: collapse; }
.header-title { font-size: 18px; font-weight: bold; color: #1c1c3a; letter-spacing: 0.5px; }
.header-sub   { font-size: 10px; color: #7f8c8d; margin-top: 3px; }
.header-meta  { font-size: 10px; color: #555; text-align: right; }
.header-badge { display: inline-block; background: #1c1c3a; color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 9px; letter-spacing: 1px; }

/* ── Section headings ─── */
.section-title {
    font-size: 12px; font-weight: bold; color: #fff;
    background: #2c3e7a; padding: 6px 12px;
    margin-bottom: 0; letter-spacing: 0.5px;
}
.section-wrap { margin-bottom: 14px; border: 1px solid #d5d9e0; }
.section-body { padding: 10px 12px; }

/* ── Flowchart nodes ─── */
.fc-table { width: 100%; border-collapse: collapse; }
.node-row  { text-align: center; }
.node-cell { padding: 2px; }

.node {
    display: inline-block;
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: bold;
    text-align: center;
}
.node-sm { padding: 5px 10px; font-size: 9.5px; }
.node-xs { padding: 4px 8px;  font-size: 9px; }

.n-start    { background: #d5f5e3; border: 1.5px solid #27ae60; color: #1a6b3c; }
.n-process  { background: #d6eaf8; border: 1.5px solid #2980b9; color: #1a3a52; }
.n-decision { background: #fef9e7; border: 1.5px solid #e67e22; color: #784212; }
.n-special  { background: #f0ebfa; border: 1.5px solid #8e44ad; color: #4a235a; }
.n-finance  { background: #ede0ff; border: 1.5px solid #6c5ecf; color: #3a237a; }
.n-done     { background: #d5f5e3; border: 2px solid #27ae60; color: #1a6b3c; }
.n-reject   { background: #fdedec; border: 1.5px solid #e74c3c; color: #922b21; }

/* ── Arrow ─── */
.arrow-cell { text-align: center; font-size: 14px; color: #95a5a6; padding: 1px; line-height: 1; }
.arr-right  { font-size: 12px; color: #95a5a6; }

/* ── Branch section: side-by-side ─── */
.branch-table { width: 100%; border-collapse: collapse; }
.branch-left  { width: 47%; vertical-align: top; padding: 6px 8px; border-right: 1px dashed #bdc3c7; }
.branch-right { width: 47%; vertical-align: top; padding: 6px 8px; }
.branch-mid   { width: 6%;  text-align: center; vertical-align: middle; font-size: 11px; color: #bdc3c7; }
.branch-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
                padding: 2px 8px; border-radius: 10px; display: inline-block; margin-bottom: 5px; }
.bl-yes { background: #e8f8f0; color: #1e8449; }
.bl-no  { background: #fef3e4; color: #ca6f1e; }

/* ── Triple branch (Finance actions) ─── */
.triple-table { width: 100%; border-collapse: collapse; }
.triple-col   { width: 33%; vertical-align: top; padding: 6px 8px; border-right: 1px dashed #bdc3c7; }
.triple-col:last-child { border-right: none; }
.triple-label { font-size: 9px; font-weight: bold; padding: 2px 8px; border-radius: 10px; display: inline-block; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
.tl-a { background: #d6eaf8; color: #1a5276; }
.tl-b { background: #fef5e7; color: #784212; }
.tl-c { background: #f5eef8; color: #6c3483; }

/* ── Reject note ─── */
.reject-note { background: #fdedec; border: 1px solid #f1948a; border-radius: 5px; padding: 7px 12px; margin-bottom: 12px; font-size: 10px; color: #6e2c2c; }

/* ── Path cards table ─── */
.paths-table { width: 100%; border-collapse: collapse; }
.path-cell   { width: 50%; vertical-align: top; padding: 5px; }
.path-card   { border-radius: 5px; padding: 9px 11px; height: 100%; }
.path-hdr    { font-size: 11px; font-weight: bold; margin-bottom: 6px; display: table; width: 100%; }
.path-badge  { display: inline-block; width: 22px; height: 22px; border-radius: 50%; text-align: center; line-height: 22px; font-size: 11px; font-weight: bold; color: #fff; margin-right: 6px; }
.path-steps  { padding-left: 16px; }
.path-steps li { margin-bottom: 3px; font-size: 10px; color: #555; }
.path-done   { font-weight: bold; color: #1e8449; }

.pc-green  { background: #f0faf4; border: 1px solid #a9dfbf; }
.pc-blue   { background: #f0f8ff; border: 1px solid #aed6f1; }
.pc-orange { background: #fef9f0; border: 1px solid #f9c784; }
.pc-purple { background: #f8f3ff; border: 1px solid #d7bde2; }
.pc-red    { background: #fdf3f3; border: 1px solid #f1948a; }
.pc-teal   { background: #f0fbfa; border: 1px solid #a2d9ce; }
.pb-green  { background: #27ae60; }
.pb-blue   { background: #2980b9; }
.pb-orange { background: #e67e22; }
.pb-purple { background: #8e44ad; }
.pb-red    { background: #e74c3c; }
.pb-teal   { background: #16a085; }

/* ── Actions table ─── */
.act-table { width: 100%; border-collapse: collapse; font-size: 10px; }
.act-table th { background: #2c3e7a; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
.act-table td { padding: 6px 8px; border-bottom: 1px solid #ecf0f1; vertical-align: top; }
.act-table tr:nth-child(even) td { background: #f8f9fc; }
.role-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9.5px; font-weight: bold; }
.rb-hod     { background: #d6eaf8; color: #1a5276; }
.rb-md      { background: #d5f5e3; color: #1a7a5e; }
.rb-gm      { background: #fef9e7; color: #7d6608; }
.rb-stb     { background: #eaf0fb; color: #2c3e7a; }
.rb-chair   { background: #f0ebfa; color: #6c3483; }
.rb-ph      { background: #fef5e7; color: #7d4607; }
.rb-pa      { background: #fdf2f8; color: #922b55; }
.rb-fh      { background: #ede0ff; color: #4a235a; }
.yes-mark   { color: #27ae60; font-weight: bold; }
.no-mark    { color: #bdc3c7; }

/* ── Status table ─── */
.status-table { width: 100%; border-collapse: collapse; }
.status-cell  { width: 25%; padding: 4px 6px; vertical-align: middle; }
.s-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; }

/* ── Footer ─── */
.page-footer { border-top: 1px solid #ddd; padding-top: 6px; margin-top: 12px; }
.footer-table { width: 100%; border-collapse: collapse; }
.footer-left  { font-size: 9px; color: #999; }
.footer-right { font-size: 9px; color: #999; text-align: right; }
</style>
</head>
<body>

{{-- ════════════════════════════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════════════════════════════ --}}
<div class="page-header">
    <table class="header-table">
        <tr>
            <td style="width:75%">
                <div class="header-title">Document Approval Flow — Complete Guide</div>
                <div class="header-sub">V-Suite · Vinayaka Mission's University · Office Document Management System</div>
            </td>
            <td style="width:25%; text-align:right; vertical-align:top">
                <div class="header-badge">OFFICIAL REFERENCE</div>
                <div class="header-meta" style="margin-top:5px">
                    Generated: {{ now()->format('d M Y, h:i A') }}<br>
                    By: {{ Auth::user()->name }}<br>
                    Dept: {{ Auth::user()->department }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     SECTION 1: VISUAL FLOWCHART
════════════════════════════════════════════════════════════════════ --}}
<div class="section-wrap">
<div class="section-title">&#9654; SECTION 1 — Approval Flowchart</div>
<div class="section-body">

<table class="fc-table">

    {{-- START --}}
    <tr><td class="node-cell text-center" colspan="3">
        <span class="node n-start">&#9654; DOCUMENT CREATED &nbsp;&mdash;&nbsp; HOD / Department Staff submits a new document request</span>
    </td></tr>
    <tr><td class="arrow-cell" colspan="3">&#9660;</td></tr>

    {{-- Select approver --}}
    <tr><td class="node-cell text-center" colspan="3">
        <span class="node n-process">&#10003; SELECT INITIAL APPROVER &nbsp;&mdash;&nbsp; Creator chooses: Medical Director &nbsp;OR&nbsp; General Manager</span>
    </td></tr>
    <tr><td class="arrow-cell" colspan="3">&#9660;</td></tr>

    {{-- Division decision --}}
    <tr><td class="node-cell text-center" colspan="3">
        <span class="node n-decision">&#9670; CLINICAL DIVISION? &nbsp;&mdash;&nbsp; Based on creator's department division</span>
    </td></tr>
    <tr><td class="arrow-cell" colspan="3">&nbsp;</td></tr>

</table>

{{-- Division branch --}}
<table class="branch-table">
    <tr>
        <td class="branch-left">
            <div class="text-center"><span class="branch-label bl-yes">YES — Clinical</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-process">&#9679; Medical Director &nbsp;&rarr;&nbsp; reviews first</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-process">&#9679; General Manager &nbsp;&rarr;&nbsp; reviews second</span></div>
        </td>
        <td class="branch-mid">&#8596;</td>
        <td class="branch-right">
            <div class="text-center"><span class="branch-label bl-no">NO — Non-Clinical</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-process">&#9679; General Manager &nbsp;&rarr;&nbsp; reviews first</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-process">&#9679; Medical Director &nbsp;&rarr;&nbsp; reviews second</span></div>
        </td>
    </tr>
</table>

<table class="fc-table">
    <tr><td class="arrow-cell text-center" colspan="3" style="padding-top:4px">&#9660; <span style="font-size:9px;color:#27ae60;font-weight:bold">BOTH PATHS CONVERGE HERE</span></td></tr>

    {{-- Payment decision --}}
    <tr><td class="node-cell text-center" colspan="3">
        <span class="node n-decision">&#9670; PAYMENT INVOLVED? &nbsp;&mdash;&nbsp; Is this document requesting a payment or purchase?</span>
    </td></tr>
    <tr><td class="arrow-cell" colspan="3">&nbsp;</td></tr>
</table>

{{-- Payment branch --}}
<table class="branch-table">
    <tr>
        <td class="branch-left">
            <div class="text-center"><span class="branch-label bl-no">NO — No Payment</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-done">&#10003;&#10003; DOCUMENT COMPLETED &nbsp;&mdash;&nbsp; Fully approved. Returned to creator.</span></div>
        </td>
        <td class="branch-mid" style="color:#27ae60;font-weight:bold;font-size:10px">YES &#9660;</td>
        <td class="branch-right">
            <div class="text-center"><span class="branch-label bl-yes">YES — Payment Required</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-decision">&#9670; AMOUNT &gt; &#8377;2,00,000? &nbsp;&mdash;&nbsp; High-value needs Chairman</span></div>
        </td>
    </tr>
</table>

<br>

{{-- Amount branch --}}
<table class="branch-table">
    <tr>
        <td class="branch-left">
            <div class="text-center"><span class="branch-label bl-yes">YES — High Value (&gt; &#8377;2L)</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-process">&#9679; STB Office &nbsp;&mdash;&nbsp; Reviews &amp; acknowledges</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-special">&#9733; Chairman &nbsp;&mdash;&nbsp; Final high-level approval. May directly select Finance Head.</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-xs n-decision">&#9670; Is it a Purchase? &nbsp;<span style="color:#27ae60">Yes</span> &rarr; Purchase Head Chennai</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-finance">&#9679; PA to Chairman &nbsp;&mdash;&nbsp; Selects Finance Head location (Salem / Chennai / Karaikal / Pondy)</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-finance" style="border-width:2px">&#9733; Finance Head (Selected Location) &nbsp;&mdash;&nbsp; Processes payment</span></div>
        </td>
        <td class="branch-mid">&#8596;</td>
        <td class="branch-right">
            <div class="text-center"><span class="branch-label bl-no">NO — Low Value (&#8804; &#8377;2L)</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-xs n-decision">&#9670; Is it a Purchase? &nbsp;<span style="color:#27ae60">Yes</span> &rarr; Purchase Head</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-finance" style="border-width:2px">&#9733; Finance Head Salem &nbsp;&mdash;&nbsp; Direct — no PA step needed</span></div>
        </td>
    </tr>
</table>

<br>
<div class="text-center" style="font-size:9px;color:#7f8c8d;font-weight:bold;letter-spacing:0.5px">&#9660; BOTH AMOUNT PATHS CONVERGE AT FINANCE HEAD &#9660;</div>
<br>

{{-- Finance Head actions --}}
<table class="triple-table">
    <tr>
        <td class="triple-col">
            <div class="text-center"><span class="triple-label tl-a">Full Payment</span></div>
            <div class="text-center" style="margin-bottom:4px"><span class="node node-sm n-finance">Records amount, date, reference no.<br>Workflow advances to next step.</span></div>
            <div class="arrow-cell text-center">&#9660;</div>
            <div class="text-center"><span class="node node-sm n-done">&#10003;&#10003; COMPLETED</span></div>
        </td>
        <td class="triple-col">
            <div class="text-center"><span class="triple-label tl-b">Partial Payment (Advance)</span></div>
            <div class="text-center" style="margin-bottom:4px"><span class="node node-sm n-finance">Advance amount noted.<br>Document stays at Finance Head<br>until full payment is made.</span></div>
            <div class="arrow-cell text-center">&#8635; Awaits balance</div>
            <div class="text-center"><span class="node node-sm n-finance" style="border-style:dashed">Finance Head — Continued<br><span style="font-size:9px">(awaits full payment)</span></span></div>
        </td>
        <td class="triple-col">
            <div class="text-center"><span class="triple-label tl-c">Forward to Another Location</span></div>
            <div class="text-center" style="margin-bottom:4px"><span class="node node-sm n-finance">Document transferred to:<br>Salem / Chennai / Karaikal / Pondy</span></div>
            <div class="arrow-cell text-center">&#8635; Re-enters at new FH</div>
            <div class="text-center"><span class="node node-sm n-finance" style="border-style:dashed">New Finance Head receives<br>&amp; processes payment</span></div>
        </td>
    </tr>
</table>

<br>
<div class="reject-note">
    <span class="bold">&#9888; REJECTION AT ANY STEP:</span>
    Any approver (Medical Director, General Manager, STB Office, Chairman, Finance Head) can
    <strong>Reject</strong> the document at any point. This immediately terminates the workflow and marks the document as
    <span style="background:#e74c3c;color:#fff;padding:1px 6px;border-radius:3px;font-size:9px;font-weight:bold">REJECTED</span>.
    The document creator is notified immediately.
</div>

</div>{{-- section-body --}}
</div>{{-- section-wrap --}}

{{-- ════════════════════════════════════════════════════════════════════
     SECTION 2: PATH-BY-PATH EXPLANATION
════════════════════════════════════════════════════════════════════ --}}
<div class="section-wrap">
<div class="section-title">&#9654; SECTION 2 — All Possible Approval Paths</div>
<div class="section-body">

<table class="paths-table">
    <tr>
        <td class="path-cell">
            <div class="path-card pc-green">
                <div class="path-hdr">
                    <span class="path-badge pb-green">A</span>
                    <span>Non-Payment Document</span>
                </div>
                <ol class="path-steps">
                    <li>Creator submits document (payment not involved)</li>
                    <li>Routes to Medical Director &rarr; General Manager (Clinical) or reverse (Non-Clinical)</li>
                    <li>Both review and approve in sequence</li>
                    <li class="path-done">&#10003; Document Completed — returned to creator</li>
                </ol>
            </div>
        </td>
        <td class="path-cell">
            <div class="path-card pc-blue">
                <div class="path-hdr">
                    <span class="path-badge pb-blue">B</span>
                    <span>Payment &le; &#8377;2,00,000 — No Purchase</span>
                </div>
                <ol class="path-steps">
                    <li>Creator submits with payment (low value)</li>
                    <li>Medical Director &rarr; General Manager (or reverse)</li>
                    <li>Forwarded directly to <strong>Finance Head Salem</strong></li>
                    <li>Finance Head processes Full or Partial payment</li>
                    <li class="path-done">&#10003; Document Completed</li>
                </ol>
            </div>
        </td>
    </tr>
    <tr>
        <td class="path-cell">
            <div class="path-card pc-orange">
                <div class="path-hdr">
                    <span class="path-badge pb-orange">C</span>
                    <span>Payment &le; &#8377;2,00,000 — With Purchase</span>
                </div>
                <ol class="path-steps">
                    <li>Creator submits with payment + purchase flag</li>
                    <li>Medical Director &rarr; General Manager (or reverse)</li>
                    <li><strong>Purchase Head</strong> reviews purchase order</li>
                    <li>Forwarded to <strong>Finance Head Salem</strong></li>
                    <li>Finance Head processes payment</li>
                    <li class="path-done">&#10003; Document Completed</li>
                </ol>
            </div>
        </td>
        <td class="path-cell">
            <div class="path-card pc-purple">
                <div class="path-hdr">
                    <span class="path-badge pb-purple">D</span>
                    <span>Payment &gt; &#8377;2,00,000 — No Purchase</span>
                </div>
                <ol class="path-steps">
                    <li>Creator submits with high-value payment</li>
                    <li>Medical Director &rarr; General Manager (or reverse)</li>
                    <li><strong>STB Office</strong> acknowledges and reviews</li>
                    <li><strong>Chairman</strong> gives final approval</li>
                    <li><strong>PA to Chairman</strong> selects Finance Head location</li>
                    <li>Selected <strong>Finance Head</strong> (any location) processes</li>
                    <li class="path-done">&#10003; Document Completed</li>
                </ol>
            </div>
        </td>
    </tr>
    <tr>
        <td class="path-cell">
            <div class="path-card pc-red">
                <div class="path-hdr">
                    <span class="path-badge pb-red">E</span>
                    <span>Payment &gt; &#8377;2,00,000 — With Purchase</span>
                </div>
                <ol class="path-steps">
                    <li>Creator submits with high-value payment + purchase</li>
                    <li>Medical Director &rarr; General Manager (or reverse)</li>
                    <li><strong>STB Office</strong> acknowledges</li>
                    <li><strong>Chairman</strong> approves</li>
                    <li><strong>Purchase Head Chennai</strong> reviews purchase order</li>
                    <li><strong>PA to Chairman</strong> selects Finance Head location</li>
                    <li>Selected <strong>Finance Head</strong> processes payment</li>
                    <li class="path-done">&#10003; Document Completed</li>
                </ol>
            </div>
        </td>
        <td class="path-cell">
            <div class="path-card pc-teal">
                <div class="path-hdr">
                    <span class="path-badge pb-teal">F</span>
                    <span>Chairman Direct Finance Head Selection</span>
                </div>
                <ol class="path-steps">
                    <li>Applies to <strong>Paths D &amp; E</strong> only</li>
                    <li>At the Chairman step, Chairman directly picks a Finance Head location</li>
                    <li>This replaces the PA to Chairman step in the sequence</li>
                    <li>Document jumps directly to the chosen Finance Head</li>
                    <li>Finance Head processes as normal</li>
                    <li class="path-done">&#10003; PA step bypassed — faster processing</li>
                </ol>
            </div>
        </td>
    </tr>
</table>

</div>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     SECTION 3: APPROVER ACTIONS TABLE
════════════════════════════════════════════════════════════════════ --}}
<div class="section-wrap">
<div class="section-title">&#9654; SECTION 3 — Available Actions at Each Approval Stage</div>
<div class="section-body">

<table class="act-table">
    <thead>
        <tr>
            <th style="width:18%">Approver / Role</th>
            <th style="width:8%;text-align:center">Approve</th>
            <th style="width:8%;text-align:center">Reject</th>
            <th style="width:8%;text-align:center">Hold</th>
            <th style="width:8%;text-align:center">Forward</th>
            <th>Special Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="role-badge rb-hod">HOD / Creator</span></td>
            <td class="text-center no-mark">—</td>
            <td class="text-center no-mark">—</td>
            <td class="text-center no-mark">—</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td>Submit document, Retract, Comment, Download PDF</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-md">Medical Director</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td>Approve in Principle, Noted, Discussion, Comment, Pending</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-gm">General Manager</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td>Approve in Principle, Noted, Discussion, Comment, Pending</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-stb">STB Office</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td>Acknowledge, Comment, Noted</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-chair">Chairman</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td><strong>Direct Finance Head selection</strong>, set Recommended/Sanctioned amounts, API-based approval from external VSuite instance</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-ph">Purchase Head</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td>Purchase Order review, Comment, Create Purchase Order / Work Order</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-pa">PA to Chairman</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td><strong>Select Finance Head location</strong> — Salem / Chennai / Karaikal / Pondy</td>
        </tr>
        <tr>
            <td><span class="role-badge rb-fh">Finance Head</span></td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td class="text-center yes-mark">&#10003;</td>
            <td><strong>Full Payment</strong> processing, <strong>Partial (Advance)</strong> recording, <strong>Forward</strong> to another Finance location</td>
        </tr>
    </tbody>
</table>

</div>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     SECTION 4: DOCUMENT STATUS VALUES
════════════════════════════════════════════════════════════════════ --}}
<div class="section-wrap">
<div class="section-title">&#9654; SECTION 4 — Document Status Reference</div>
<div class="section-body">

<table class="status-table">
    <tr>
        <td class="status-cell"><span class="s-badge" style="background:#fef9e7;color:#9a7d0a;border:1px solid #f0c040">Draft</span>&nbsp; Not yet submitted</td>
        <td class="status-cell"><span class="s-badge" style="background:#d6eaf8;color:#1a5276;border:1px solid #85c1e9">Sent to X</span>&nbsp; Awaiting X's action</td>
        <td class="status-cell"><span class="s-badge" style="background:#fef5e7;color:#784212;border:1px solid #f0b27a">Hold</span>&nbsp; Paused by approver</td>
        <td class="status-cell"><span class="s-badge" style="background:#fdedec;color:#922b21;border:1px solid #f1948a">Rejected</span>&nbsp; Terminated — rejected</td>
    </tr>
    <tr>
        <td class="status-cell"><span class="s-badge" style="background:#d5f5e3;color:#1a6b3c;border:1px solid #82e0aa">Completed</span>&nbsp; All approvals done</td>
        <td class="status-cell"><span class="s-badge" style="background:#f4ecf7;color:#6c3483;border:1px solid #c39bd3">Closed</span>&nbsp; Closed by creator</td>
        <td class="status-cell"><span class="s-badge" style="background:#eaf0fb;color:#2c3e7a;border:1px solid #a9b4d4">Discussion</span>&nbsp; Sent for discussion</td>
        <td class="status-cell"><span class="s-badge" style="background:#fdfefe;color:#616a6b;border:1px solid #bdc3c7">Retracted</span>&nbsp; Pulled back by creator</td>
    </tr>
    <tr>
        <td class="status-cell"><span class="s-badge" style="background:#fef9e7;color:#7d6608;border:1px solid #f0c040">Pending</span>&nbsp; Marked pending at stage</td>
        <td class="status-cell"><span class="s-badge" style="background:#edf7f6;color:#148f77;border:1px solid #76d7c4">Payment In Progress</span>&nbsp; Finance processing</td>
        <td class="status-cell"><span class="s-badge" style="background:#d5f5e3;color:#1a6b3c;border:1px solid #82e0aa">Paid</span>&nbsp; Full payment done</td>
        <td class="status-cell"><span class="s-badge" style="background:#fef5e7;color:#784212;border:1px solid #f0b27a">Advance Paid</span>&nbsp; Partial payment recorded</td>
    </tr>
</table>

</div>
</div>

{{-- ════════════════════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════════════════ --}}
<div class="page-footer">
    <table class="footer-table">
        <tr>
            <td class="footer-left">
                V-Suite — Vinayaka Mission's University Document Approval System &nbsp;|&nbsp;
                This document is system-generated and reflects the current workflow configuration.
            </td>
            <td class="footer-right">
                Page <span style="font-size:9px">1</span> &nbsp;|&nbsp;
                Printed: {{ now()->format('d-m-Y H:i') }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>
