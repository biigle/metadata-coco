<script>
import {handleErrorResponse} from './import';

export default {
    data() {
        return {
            quote: '',
        };
    },
    methods: {
        refreshQuote() {
            this.$http.get('quotes/new').then(this.handleResponse, handleErrorResponse);
        },
        handleResponse(response) {
            this.quote = response.body;
        },
    },
    created() {
        this.refreshQuote();
    },
};
</script>
