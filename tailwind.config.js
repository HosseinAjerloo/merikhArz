/** @type {import('tailwindcss').Config} */
export default {
    content: [ "./resources/**/*.blade.php",],
    theme: {
        extend: {
            fontSize:{
                min:'0.750rem',
                min_sm:'0.650rem'
            },
            fontFamily: {
                'vazir': ['vazir'],
                'iranSans': ['iranSans'],
                'yekan': ['yekan'],

            },
            colors:{
                black_blur:'rgba(0,0,0, 0.7)',
                'F5F5F5':'#F5F5F5'
            },

        },
    },
    plugins: [],
}




