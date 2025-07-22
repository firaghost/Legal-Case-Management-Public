# LCMS – Final Deployment Checklist

Use this checklist before every release or promotion from staging ➜ production. Tick off each item to ensure a smooth, secure, and fully-functional deployment.

---

## ✅ Backend
- [ ] Laravel API container/host is live and reachable.
- [ ] `.env` points to **production** database & cache servers.
- [ ] All migrations executed and DB seeded with minimal reference/test data.
- [ ] Role-based middleware (lawyer, supervisor, admin) tested with real accounts.
- [ ] File-upload paths (`storage/app/...`) secured & validated; symbolic links (`storage:link`) created.
- [ ] CORS policy only allows approved front-end domains.
- [ ] HTTPS enforced; HSTS, secure cookies on.
- [ ] Queue / scheduler workers running (notifications, exports, observers).

## ✅ Frontend
- [ ] React/Vite build completed (`npm run build`) – outputs hashed assets in `public/build`.
- [ ] Environment variables (`VITE_API_URL`, etc.) set for prod.
- [ ] All routes wrapped in `ProtectedRoute` guards.
- [ ] Sidebar and navigation render correctly per role.
- [ ] Error boundaries tested (network + render failures).
- [ ] Optional PWA manifest & service-worker build verified.

## ✅ Reports & Exports
- [ ] All export buttons (PDF, Excel, CSV) tested with real data.
- [ ] Summary and performance charts rendering accurately.
- [ ] DOMPDF & spreadsheet libraries working on server (fonts, temp paths).

## ✅ Authentication
- [ ] JWT/login flow confirmed; token storage uses `httpOnly` cookies or secure localStorage.
- [ ] Token refresh & expiry handling validated.
- [ ] Logout clears tokens & redirects to `/login`.

## ✅ File Uploads
- [ ] Evidence / advisory uploads accept only PDF, JPG, PNG.
- [ ] Max size limits enforced server-side & client-side.
- [ ] Uploaded files virus-scanned (if service available).

## ✅ UI & Functional Tests
- [ ] Case Entry validation rules fire (required, pattern, max-length).
- [ ] Timeline interactions (add progress, hearings) work on mobile + desktop.
- [ ] Supervisor Approval modals (execution / early closure) approve & reject as expected.
- [ ] Admin Audit Log filtering & export operate.
- [ ] Advisory dashboard tabs & forms load and persist data.

## ✅ Admin Readiness
- [ ] User Management: create, edit, deactivate flows tested.
- [ ] Role changes propagate permissions immediately (re-login if required).
- [ ] System Settings (court names, branches) editable & saved.

## ✅ Documentation & Training
- [ ] Updated **User Manual** (PDF) stored in /docs & shared.
- [ ] Training slide deck reviewed with stakeholders.
- [ ] Internal API documentation published (Swagger / Postman) if exposed.

## ✅ Hosting & Ops
- [ ] Staging & production environments deployed and pass smoke tests.
- [ ] All `.env` secrets injected via CI/CD or hosting dashboard – **never committed**.
- [ ] Application, queue & scheduler logs monitored (e.g., Laravel Telescope / Sentry).
- [ ] Automated nightly backups enabled for DB & file storage; restore tested.

## ✅ Launch
- [ ] Live demo completed with key stakeholders.
- [ ] Pilot group test results reviewed; critical issues resolved.
- [ ] Feedback loop & support channel established (email / ticketing).
- [ ] Final go-live sign-off received and deployment window scheduled.

---

**Remember:** After go-live, schedule a post-deployment review to capture lessons learned and update this checklist accordingly.






