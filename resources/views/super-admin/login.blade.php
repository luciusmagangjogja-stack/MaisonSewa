<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login Super Admin — SewaJas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif:  ['"Cormorant Garamond"', 'Georgia', 'serif'],
                        sans:   ['"DM Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        bark:  { DEFAULT:'#3A2210', light:'#5C3D1E', dark:'#2B1608' },
                        gold:  { lt:'#F0D48A', DEFAULT:'#C9A84C', dk:'#9E7A2C' },
                        cream: { DEFAULT:'#FAF6F0', warm:'#F2EBE0', sand:'#E6D8C5' },
                    },
                    boxShadow: {
                        'card':    '0 0 0 8px rgba(201,168,76,.06), 0 24px 60px rgba(43,22,8,.13)',
                        'card-sm': '0 4px 24px rgba(43,22,8,.10)',
                        'btn':     '0 4px 18px rgba(58,34,16,.32)',
                        'btn-h':   '0 8px 28px rgba(58,34,16,.42)',
                    },
                    screens: {
                        'xs': '400px',
                    }
                }
            }
        }
    </script>

    <style>
        /* ── base reset ── */
        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body { font-family: 'DM Sans', system-ui, sans-serif; }

        /* ── luxury dot pattern ── */
        .dot-pattern {
            background-image: radial-gradient(circle, rgba(201,168,76,.13) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* ── diagonal stripe ── */
        .stripe-pattern {
            background-image: repeating-linear-gradient(
                -45deg,
                transparent,
                transparent 12px,
                rgba(201,168,76,.04) 12px,
                rgba(201,168,76,.04) 24px
            );
        }

        /* ── glowing orbs ── */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
        }

        /* ── input focus ring ── */
        .inp {
            display: block;
            width: 100%;
            padding: .72rem 1rem .72rem 2.6rem;
            background: #FAF6F0;
            border: 1.5px solid #E6D8C5;
            border-radius: .75rem;
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem;
            color: #2B1608;
            outline: none;
            transition: border-color .18s, box-shadow .18s, background .18s;
            -webkit-appearance: none;
        }
        .inp:focus {
            border-color: #C9A84C;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(201,168,76,.14);
        }
        .inp::placeholder { color: #C0B4A8; }

        /* ── animations ── */
        @keyframes float {
            0%,100% { transform: translateY(0) rotate(-.6deg); }
            50%      { transform: translateY(-10px) rotate(.6deg); }
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        .float-anim { animation: float 5s ease-in-out infinite; }
        .fade-up    { animation: fadeUp .5s ease both; }
        .fade-up-1  { animation: fadeUp .5s .1s ease both; }
        .fade-up-2  { animation: fadeUp .5s .2s ease both; }
        .fade-up-3  { animation: fadeUp .5s .3s ease both; }
        .spin-anim  { animation: spin 1s linear infinite; }

        /* ── left panel gradient overlay ── */
        .lp-overlay {
            background: linear-gradient(
                160deg,
                rgba(92,61,30,.10) 0%,
                rgba(43,22,8,.55)  60%,
                rgba(43,22,8,.85)  100%
            );
        }

        /* ── circle ring ── */
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(201,168,76,.18);
            pointer-events: none;
        }

        /* ── gold divider line ── */
        .gold-line {
            width: 44px;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, #C9A84C, transparent);
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen flex overflow-x-hidden bg-cream-warm" x-data="loginPage()">

    <!-- MOBILE HEADER -->
    <div class="lg:hidden fixed top-0 inset-x-0 z-30 flex items-center gap-3 px-4 py-3
                bg-bark-dark border-b border-gold/20 shadow-md">
        <svg width="32" height="32" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0">
            <path d="M5,20 L5,72 L38,72 L38,38 L5,20 Z"        fill="#3A2210"/>
            <path d="M75,20 L75,72 L42,72 L42,38 L75,20 Z"       fill="#3A2210"/>
            <path d="M5,20 L30,16 L38,38 L5,20 Z"                fill="#5C3D1E"/>
            <path d="M75,20 L50,16 L42,38 L75,20 Z"              fill="#5C3D1E"/>
            <path d="M30,16 L50,16 L42,38 L42,72 L38,72 L38,38 Z" fill="#F0EAE0"/>
            <path d="M38,38 L40,33 L42,38 L41,44 L39,44 Z"       fill="#C9A84C"/>
            <path d="M39,44 L37,64 L40,68 L43,64 L41,44 Z"       fill="#C9A84C"/>
            <circle cx="40" cy="54" r="2.2" fill="#C9A84C"/>
        </svg>
        <div>
                <span class="font-serif text-lg font-bold text-cream-DEFAULT leading-none">
                    Sewa<span class="text-gold italic">Jas</span>
                </span>
            <p class="text-gold/50 text-[10px] tracking-widest uppercase leading-none mt-0.5">Premium Fashion Rental</p>
        </div>
    </div>

    <!-- MAIN LAYOUT -->
    <div class="flex flex-col lg:flex-row w-full min-h-screen">

        <!-- LEFT PANEL -->
        <aside class="hidden lg:flex lg:w-5/12 xl:w-[42%] flex-col relative overflow-hidden
                      bg-bark-dark flex-shrink-0">
            <div class="absolute inset-0 stripe-pattern opacity-60"></div>
            <div class="lp-overlay absolute inset-0"></div>
            <div class="ring" style="width:320px;height:320px;top:-100px;left:-100px;"></div>
            <div class="ring" style="width:200px;height:200px;bottom:-60px;right:-60px;"></div>
            <div class="ring" style="width:100px;height:100px;top:40%;left:5%;"></div>
            <div class="orb w-48 h-48 bg-gold/10 top-10 left-10"></div>
            <div class="orb w-32 h-32 bg-gold/8 bottom-20 right-10"></div>
            <div class="absolute left-[18%] top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-gold/25 to-transparent"></div>
            <div class="absolute right-[22%] top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-gold/15 to-transparent"></div>

            <div class="relative z-10 flex flex-col items-center justify-center flex-1 px-8 xl:px-12 py-12 text-center">
                <div class="float-anim fade-up mb-6">
                    <svg width="150" height="165" viewBox="0 0 80 88" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="40" cy="85" rx="28" ry="4.5" fill="rgba(0,0,0,0.25)"/>
                        <path d="M5,20 L3,78 L38,78 L38,40 L5,20 Z" fill="#2B1608"/>
                        <path d="M75,20 L77,78 L42,78 L42,40 L75,20 Z" fill="#2B1608"/>
                        <path d="M5,20 L30,16 L38,40 Z" fill="#5C3D1E"/>
                        <path d="M75,20 L50,16 L42,40 Z" fill="#5C3D1E"/>
                        <path d="M30,16 L50,16 L42,40 L42,78 L38,78 L38,40 Z" fill="#F0EAE0"/>
                        <circle cx="40" cy="56" r="2.5" fill="#C9A84C"/>
                        <circle cx="40" cy="66" r="2.5" fill="#C9A84C"/>
                    </svg>
                </div>

                <div class="fade-up-1">
                    <h1 class="font-serif text-4xl xl:text-5xl font-bold text-cream-DEFAULT tracking-wide leading-none">
                        Sewa<span class="text-gold-lt italic">Jas</span>
                    </h1>
                    <div class="flex items-center justify-center gap-3 mt-3">
                        <div class="gold-line"></div>
                        <p class="text-gold/55 text-[11px] tracking-[.22em] uppercase whitespace-nowrap">Super Admin Portal</p>
                        <div class="gold-line"></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- RIGHT PANEL -->
        <main class="flex-1 flex items-center justify-center bg-cream-DEFAULT dot-pattern px-4 pt-20 pb-6 lg:pt-6 lg:pb-6 min-h-screen lg:min-h-0 relative overflow-hidden">
            <div class="relative z-10 w-full max-w-[420px] fade-up">
                <div class="bg-white rounded-2xl shadow-card border border-cream-sand/70 overflow-hidden">
                    <div class="h-1 w-full bg-gradient-to-r from-gold/30 via-gold to-gold/30"></div>
                    <div class="px-6 xs:px-8 pt-7 pb-8">
                        <div class="mb-6 fade-up-1">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="gold-line"></div>
                                <span class="text-gold text-[11px] tracking-[.18em] uppercase font-semibold whitespace-nowrap">
                                    Super Admin
                                </span>
                                <div class="gold-line"></div>
                            </div>
                            <h2 class="font-serif text-[1.85rem] xs:text-[2rem] font-bold text-bark-dark leading-tight">
                                Masuk ke<br><em class="text-gold-dk not-italic font-bold">Portal Utama</em>
                            </h2>
                        </div>

                        <!-- Alerts -->
                        @if ($errors->any())
                        <div class="mb-5 flex gap-2.5 items-start bg-red-50 border border-red-200 rounded-xl p-3.5 text-sm text-red-600">
                            <div>@foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}" @submit="loading = true" class="space-y-4 fade-up-2">
                            @csrf
                            <div>
                                <label for="email" class="block text-[11px] font-semibold tracking-[.1em] uppercase text-bark-light mb-1.5">Email</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="superadmin@jasrental.id" required autofocus class="inp">
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-[11px] font-semibold tracking-[.1em] uppercase text-bark-light mb-1.5">Password</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </span>
                                    <input :type="showPass ? 'text' : 'password'" id="password" name="password" placeholder="••••••••" required class="inp pr-11">
                                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-bark transition-colors p-1">
                                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" :disabled="loading" class="relative w-full flex items-center justify-center gap-2 bg-bark-dark text-gold-lt rounded-xl py-3.5 px-6 font-semibold text-sm tracking-wide shadow-btn transition-all duration-200 hover:bg-bark hover:shadow-btn-h hover:-translate-y-0.5 disabled:opacity-60 overflow-hidden group">
                                <template x-if="!loading">
                                    <span class="flex items-center gap-2">Masuk ke Portal</span>
                                </template>
                                <template x-if="loading">
                                    <span class="flex items-center gap-2">Memproses...</span>
                                </template>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function loginPage() {
            return { showPass: false, loading: false }
        }
    </script>
</body>
</html>
