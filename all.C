/****************************************************/
/* File: parse.c                                    */
/* The parser implementation for the TINY compiler  */
/* Compiler Construction: Principles and Practice   */
/* Kenneth C. Louden                                */
/****************************************************/

/****************************************************/
/* File: globals.h                                  */
/* Global types and vars for TINY compiler          */
/* must come before other include files             */
/* Compiler Construction: Principles and Practice   */
/* Kenneth C. Louden                                */
/****************************************************/
#define _CRT_SECURE_NO_WARNINGS

#ifndef _GLOBALS_H_
#define _GLOBALS_H_

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>

#ifndef FALSE
#define FALSE 0
#endif

#ifndef TRUE
#define TRUE 1
#endif

/* MAXRESERVED = the number of reserved words */
#define MAXRESERVED 23

typedef enum
/* book-keeping tokens */
{
    ENDFILE, ERROR,
    /* reserved words */
    IF, THEN, ELSE, END, REPEAT, UNTIL, READ, WRITE,
    T_TRUE, T_FALSE, OR, AND, NOT, INT, BOOL, STRING, DO, WHILE, INCLUDE, BREAK, CONTINUE,
    /* multicharacter tokens */
    ID, NUM, STR,
    /* special symbols */
    ASSIGN, EQ, LT, GT, LTE, GTE, PLUS, MINUS, TIMES, OVER, LPAREN, RPAREN, SEMI, COMMA, SQM, PERCENT, DOUBLETIMES,
    /*新增保留字*/
    FLOAT, DOUBLE
} TokenType;

extern FILE* source; /* source code text file */
extern FILE* listing; /* listing output text file */
extern FILE* code; /* code text file for TM simulator */

extern int lineno; /* source line number for listing */

/**************************************************/
/***********   Syntax tree for parsing ************/
/**************************************************/

typedef enum { StmtK, ExpK } NodeKind;
typedef enum { IfK, RepeatK, AssignK, ReadK, WriteK, WhileK, DeclK, StartK } StmtKind;
typedef enum { OpK, ConstK, IdK } ExpKind;

/* ExpType is used for type checking */
typedef enum { Void, Integer, Boolean, Float, Double, String} ExpType;

#define MAXCHILDREN 3

typedef struct treeNode
{
    struct treeNode* child[MAXCHILDREN];
    struct treeNode* sibling;
    int lineno;
    NodeKind nodekind;
    union { StmtKind stmt; ExpKind exp; } kind;
    union {
        TokenType op;
        int val;
        char* name;
    } attr;
    ExpType type; /* for type checking of exps */
} TreeNode;

/**************************************************/
/***********   Flags for tracing       ************/
/**************************************************/

/* EchoSource = TRUE causes the source program to
 * be echoed to the listing file with line numbers
 * during parsing
 */
 /***  Error **/
#define MAX_ERROR 6
extern int errorCode;
//extern char* errorMsg[MAX_ERROR];

extern int EchoSource;

/* TraceScan = TRUE causes token information to be
 * printed to the listing file as each token is
 * recognized by the scanner
 */
extern int TraceScan;

/* TraceParse = TRUE causes the syntax tree to be
 * printed to the listing file in linearized form
 * (using indents for children)
 */
extern int TraceParse;

/* TraceAnalyze = TRUE causes symbol table inserts
 * and lookups to be reported to the listing file
 */
extern int TraceAnalyze;

/* TraceCode = TRUE causes comments to be written
 * to the TM code file as code is generated
 */
extern int TraceCode;

/* Error = TRUE prevents further passes if an error occurs */
extern int Error;
#endif

/****************************************************/
/* File: util.h                                     */
/* Utility functions for the TINY compiler          */
/* Compiler Construction: Principles and Practice   */
/* Kenneth C. Louden                                */
/****************************************************/

#ifndef _UTIL_H_
#define _UTIL_H_

/* Procedure printToken prints a token
 * and its lexeme to the listing file
 */
void printToken(TokenType, const char*);

/* Function newStmtNode creates a new statement
 * node for syntax tree construction
 */
TreeNode* newStmtNode(StmtKind);

/* Function newExpNode creates a new expression
 * node for syntax tree construction
 */
TreeNode* newExpNode(ExpKind);

/* Function copyString allocates and makes a new
 * copy of an existing string
 */
char* copyString(char*);

/* procedure printTree prints a syntax tree to the
 * listing file using indentation to indicate subtrees
 */
void printTree(TreeNode*);

#endif

/****************************************************/
/* File: scan.h                                     */
/* The scanner interface for the TINY compiler      */
/* Compiler Construction: Principles and Practice   */
/* Kenneth C. Louden                                */
/****************************************************/

#ifndef _SCAN_H_
#define _SCAN_H_

/* MAXTOKENLEN is the maximum size of a token */
#define MAXTOKENLEN 40

/* tokenString array stores the lexeme of each token */
extern char tokenString[MAXTOKENLEN + 1];

/* function getToken returns the
 * next token in source file
 */
TokenType getToken(void);

#endif
/****************************************************/
/* File: parse.h                                    */
/* The parser interface for the TINY compiler       */
/* Compiler Construction: Principles and Practice   */
/* Kenneth C. Louden                                */
/****************************************************/

#ifndef _PARSE_H_
#define _PARSE_H_

/* Function parse returns the newly
 * constructed syntax tree
 */
TreeNode* parse(void);

#endif

static TokenType token; /* holds current token */
/* function prototypes for recursive calls */
static TreeNode* stmt_sequence(void);
static TreeNode* statement(void);
static TreeNode* if_stmt(void);
static TreeNode* repeat_stmt(void);
static TreeNode* assign_stmt(void);
static TreeNode* read_stmt(void);
static TreeNode* write_stmt(void);
static TreeNode* exp(void);
static TreeNode* simple_exp(void);
static TreeNode* term(void);
static TreeNode* factor(void);

static TreeNode* while_stmt(void);
static TreeNode* varlist(void);
static TreeNode* declaration(void);
static TreeNode* decl(void);
const char* errorMsg[6] = {
    "Unkown error",
    "Uncomplete comment,} expected!",
    "Comment error,{ unexpected!",
    "Uncomplete string, ' expected!",
    "String can not contain RETURN",
    "Illegal character",
};

/* Procedure printToken prints a token
 * and its lexeme to the listing file
 */
void printToken(TokenType token, const char* tokenString)
{
    switch (token)
    {
    case IF:
    case THEN:
    case ELSE:
    case END:
    case REPEAT:
    case UNTIL:
    case READ:
    case WRITE:
    case T_TRUE:
    case T_FALSE:
    case OR:
    case AND:
    case NOT:
    case INT:
    case BOOL:
    case STRING:
    case DO:
    case WHILE:
        fprintf(listing,
            "reserved word: %s\n", tokenString);
        break;
    case ASSIGN: fprintf(listing, ":=\n"); break;

    case LT: fprintf(listing, "<\n"); break;
    case EQ: fprintf(listing, "=\n"); break;
    case GT: fprintf(listing, ">\n"); break;
    case LTE: fprintf(listing, "<=\n"); break;
    case GTE: fprintf(listing, ">=\n"); break;

    case LPAREN: fprintf(listing, "(\n"); break;
    case RPAREN: fprintf(listing, ")\n"); break;
    case SEMI: fprintf(listing, ";\n"); break;
    case COMMA: fprintf(listing, ",\n"); break;
    case SQM: fprintf(listing, "\'\n"); break;
    case PLUS: fprintf(listing, "+\n"); break;
    case MINUS: fprintf(listing, "-\n"); break;
    case TIMES: fprintf(listing, "*\n"); break;
    case OVER: fprintf(listing, "/\n"); break;
    case ENDFILE: fprintf(listing, "EOF\n"); break;
    case NUM:
        fprintf(listing,
            "NUM, val= %s\n", tokenString);
        break;
    case ID:
        fprintf(listing,
            "ID, name= %s\n", tokenString);
        break;
    case STR:
        fprintf(listing, "STR,name= %s\n", tokenString);
        break;
    case ERROR: {

        fprintf(listing,
            "ERROR %s :%s\n", errorMsg[errorCode], tokenString);


    }break;
    case DOUBLE:
        if (tokenString=="double")
        {
            fprintf(listing,"reserved word: %s\n",tokenString);
        }
        else
        {
            fprintf(listing,"DOUBLE NUM,val= %s\n",tokenString);
        }
        break;

    default: /* should never happen */
        fprintf(listing, "Unknown token: %d\n", token);
    }
}

/* Function newStmtNode creates a new statement
 * node for syntax tree construction
 */
TreeNode* newStmtNode(StmtKind kind)
{
    TreeNode* t = (TreeNode*)malloc(sizeof(TreeNode));
    int i;
    if (t == NULL)
        fprintf(listing, "Out of memory error at line %d\n", lineno);
    else {
        for (i = 0; i < MAXCHILDREN; i++) t->child[i] = NULL;
        t->sibling = NULL;
        t->nodekind = StmtK;
        t->kind.stmt = kind;
        t->lineno = lineno;
    }
    return t;
}

/* Function newExpNode creates a new expression
 * node for syntax tree construction
 */
TreeNode* newExpNode(ExpKind kind)
{
    TreeNode* t = (TreeNode*)malloc(sizeof(TreeNode));
    int i;
    if (t == NULL)
        fprintf(listing, "Out of memory error at line %d\n", lineno);
    else {
        for (i = 0; i < MAXCHILDREN; i++) t->child[i] = NULL;
        t->sibling = NULL;
        t->nodekind = ExpK;
        t->kind.exp = kind;
        t->lineno = lineno;
        t->type = Void;
    }
    return t;
}

/* Function copyString allocates and makes a new
 * copy of an existing string
 */
char* copyString(char* s)
{
    int n;
    char* t;
    if (s == NULL) return NULL;
    n = strlen(s) + 1;
    t = (char*)malloc(n);
    if (t == NULL)
        fprintf(listing, "Out of memory error at line %d\n", lineno);
    else strcpy(t, s);
    return t;
}

/* Variable indentno is used by printTree to
 * store current number of spaces to indent
 */
static int indentno = 0;

/* macros to increase/decrease indentation */
#define INDENT indentno+=2
#define UNINDENT indentno-=2

/* printSpaces indents by printing spaces */
static void printSpaces(void)
{
    int i;
    for (i = 0; i < indentno; i++)
        fprintf(listing, " ");
}

/* procedure printTree prints a syntax tree to the
 * listing file using indentation to indicate subtrees
 */
void printTree(TreeNode* tree)
{
    int i;
    INDENT;
    while (tree != NULL) {
        printSpaces();
        if (tree->nodekind == StmtK)
        {
            switch (tree->kind.stmt) {
            case IfK:
                fprintf(listing, "If\n");
                break;
            case RepeatK:
                fprintf(listing, "Repeat\n");
                break;
            case AssignK:
                fprintf(listing, "Assign to: %s\n", tree->attr.name);
                break;
            case ReadK:
                fprintf(listing, "Read: %s\n", tree->attr.name);
                break;
            case WriteK:
                fprintf(listing, "Write\n");
                break;
            case WhileK:
                fprintf(listing, "While\n");
                break;
            case DeclK:
                fprintf(listing, "Type: %s\n",tree->attr.name);
                break;
            default:
                fprintf(listing, "Unknown ExpNode kind\n");
                break;
            }
        }
        else if (tree->nodekind == ExpK)
        {
            switch (tree->kind.exp) {
            case OpK:
                fprintf(listing, "Op: ");
                printToken(tree->attr.op, "\0");
                break;
            case ConstK:
                fprintf(listing, "Const: %d\n", tree->attr.val);
                switch (tree->type)
                {
                case Integer:
                    fprintf(listing, "Integer: %d\n", tree->attr.val);
                    break;
                case String:
                    fprintf(listing, "String: %s\n", tree->attr.name);
                    break;
                case Double:
                    fprintf(listing, "Double: %s\n", tree->attr.name);
                    break;
                case Boolean:
                    fprintf(listing, "Bool: %s\n", tree->attr.name);
                    break;                      
                }
                break;
            case IdK:
                fprintf(listing, "Id: %s\n", tree->attr.name);
                break;      
            default:
                fprintf(listing, "Unknown ExpNode kind\n");
                break;
            }
        }
        else fprintf(listing, "Unknown node kind\n");
        for (i = 0; i < MAXCHILDREN; i++)
            printTree(tree->child[i]);
        tree = tree->sibling;
    }
    UNINDENT;
}





int isLegalChar(char c) {


    return (isalnum(c) ||
        isspace(c) ||
        c == '>' ||
        c == '<' ||
        c == '=' ||
        c == ',' ||
        c == ';' ||
        c == '\'' ||
        c == '{' ||
        c == '}' ||
        c == '+' ||
        c == '-' ||
        c == '*' ||
        c == '/' ||
        c == '(' ||
        c == ')'
        );


}

/* states in scanner DFA */
typedef enum
{
    START, INASSIGN, INCOMMENT, INNUM, INID, INGREAT, INLESS, INSTR, DONE, ININT, INBOOL, INFLOAT, INDOUBLE
}
StateType;

/* lexeme of identifier or reserved word */
char tokenString[MAXTOKENLEN + 1];

/* BUFLEN = length of the input buffer for
   source code lines */
#define BUFLEN 256

static char lineBuf[BUFLEN]; /* holds the current line */
static int linepos = 0; /* current position in LineBuf */
static int bufsize = 0; /* current size of buffer string */
static int EOF_flag = FALSE; /* corrects ungetNextChar behavior on EOF */

/* getNextChar fetches the next non-blank character
   from lineBuf, reading in a new line if lineBuf is
   exhausted */
static int getNextChar(void)
{
    if (!(linepos < bufsize))
    {
        lineno++;
        if (fgets(lineBuf, BUFLEN - 1, source))
        {
            if (EchoSource) fprintf(listing, "%4d: %s", lineno, lineBuf);
            bufsize = strlen(lineBuf);
            linepos = 0;
            return lineBuf[linepos++];
        }
        else
        {
            EOF_flag = TRUE;
            return EOF;
        }
    }
    else return lineBuf[linepos++];
}

/* ungetNextChar backtracks one character
   in lineBuf */
static void ungetNextChar(void)
{
    if (!EOF_flag) linepos--;
}

/* lookup table of reserved words */
static struct
{
    const   char* str;
    TokenType tok;
} reservedWords[MAXRESERVED]
= { {"if",IF},{"then",THEN},{"else",ELSE},{"end",END},
   {"repeat",REPEAT},{"until",UNTIL},{"read",READ},
   {"write",WRITE},
 {"true",T_TRUE},
 {"false",T_FALSE},
 {"not",NOT},
 {"and",AND},
 {"or",OR},
 {"int",INT},
 {"string",STRING},
 {"bool",BOOL},
 {"do",DO},
 {"while",WHILE},
 {"include",INCLUDE}, {"break",BREAK}, {"continue",CONTINUE}, {"float",FLOAT}, {"double",DOUBLE}
};

/* lookup an identifier to see if it is a reserved word */
/* uses linear search */
static TokenType reservedLookup(char* s)
{
    int i;
    for (i = 0; i < MAXRESERVED; i++)
        if (!strcmp(s, reservedWords[i].str))
            return reservedWords[i].tok;
    return ID;
}


/* Error code part **/

int errorCode = 0;

#define ERR_UNKOWN 0
#define ERR_COMMENT_US 1
#define ERR_COMMENT_CE 2
#define ERR_STRING_US 3
#define ERR_STRING_RETURN 4
#define ERR_CHAR_IL 5

/*                 */


/****************************************/
/* the primary function of the scanner  */
/****************************************/
/* function getToken returns the
 * next token in source file
 */
TokenType getToken(void)
{  /* index for storing into tokenString */
    int tokenStringIndex = 0;
    /* holds current token to be returned */
    TokenType currentToken;
    /* current state - always begins at START */
    StateType state = START;
    /* flag to indicate save to tokenString */

    int save;
    while (state != DONE)
    {
        int c = getNextChar();
        save = TRUE;
        switch (state)
        {
        case START:
            if (isdigit(c))
                state = INNUM;
            else if (isalpha(c))
                state = INID;
            else if (c == '<')
                state = INLESS;
            else if (c == '>')
                state = INGREAT;
            else if (c == '\'') {
                state = INSTR;
            }
            else if (c == ':')
                state = INASSIGN;
            else if ((c == ' ') || (c == '\t') || (c == '\n') || (c == '\r'))
                save = FALSE;
            else if (c == '{')
            {
                save = FALSE;
                state = INCOMMENT;
            }
            else
            {
                state = DONE;

                switch (c)

                {
                case EOF:
                    save = FALSE;
                    currentToken = ENDFILE;
                    break;
                case '=':
                    currentToken = EQ;
                    break;
                case '+':
                    currentToken = PLUS;
                    break;
                case '-':
                    currentToken = MINUS;
                    break;
                case '*':
                    currentToken = TIMES;
                    break;
                case '/':
                    currentToken = OVER;
                    break;
                case '(':
                    currentToken = LPAREN;
                    break;
                case ')':
                    currentToken = RPAREN;
                    break;
                case ';':
                    currentToken = SEMI;
                    break;
                case ',':
                    currentToken = COMMA;
                    break;
                default:
                    if (!isLegalChar(c)) {
                        currentToken = ERROR;
                        errorCode = ERR_CHAR_IL;
                        break;
                    }
                    currentToken = ERROR;
                    errorCode = ERR_UNKOWN;
                    break;
                }


            }
            break;
        case INCOMMENT:
            save = FALSE;
            if (c == EOF)
            {
                state = DONE;
                currentToken = ERROR;
                errorCode = ERR_COMMENT_US;
            }
            else if (c == '}') state = START;
            else if (c == '{') {
                state = DONE;
                currentToken = ERROR;
                errorCode = ERR_COMMENT_CE;
            }
            break;
        case INASSIGN:
            state = DONE;
            if (c == '=')
                currentToken = ASSIGN;
            else
            { /* backup in the input */
                ungetNextChar();
                save = FALSE;
                currentToken = ERROR;
            }
            break;
        case INNUM:
            if (c=='.')
            {
                state = INDOUBLE;
            }
            // 读到小数点转换为DOUBLE类型
            if (!isdigit(c))
            { /* backup in the input */
                ungetNextChar();
                save = FALSE;
                state = DONE;
                currentToken = NUM;
            }
            break;
        case INDOUBLE:
            if (!isdigit(c))
            { /* backup in the input */
                ungetNextChar();
                save = FALSE;
                state = DONE;
                currentToken = DOUBLE;
            }
            break;
        case INID:
            if (!isalpha(c) && !isdigit(c))
            { /* backup in the input */
                ungetNextChar();
                save = FALSE;
                state = DONE;
                currentToken = ID;
            }
            break;

        case INLESS:
            state = DONE;
            if (c == '=')
                currentToken = LTE;
            else
            {
                ungetNextChar();
                currentToken = LT;
                save = FALSE;

            }
            break;
        case INGREAT:
            state = DONE;
            if (c == '=')
                currentToken = GTE;
            else
            {
                ungetNextChar();
                currentToken = GT;
                save = FALSE;
            }
            break;

        case INSTR:
            if (c == '\'') {
                currentToken = STR;
                state = DONE;
            }
            else if (c == '\n')  // return 
            {
                ungetNextChar();
                currentToken = ERROR;
                errorCode = ERR_STRING_RETURN;
                save = FALSE;
                state = DONE;
            }
            else if (c == EOF) {
                currentToken = ERROR;
                errorCode = ERR_STRING_US;
                save = FALSE;
                state = DONE;
            }
            break;
        case DONE:
        default: /* should never happen */
            fprintf(listing, "Scanner Bug: state= %d\n", state);
            state = DONE;
            currentToken = ERROR;
            break;
        }
        if ((save) && (tokenStringIndex <= MAXTOKENLEN))
            tokenString[tokenStringIndex++] = (char)c;
        if (state == DONE)
        {
            tokenString[tokenStringIndex] = '\0';
            if (currentToken == ID)
                currentToken = reservedLookup(tokenString);
        }
    }
    if (TraceScan) {
        fprintf(listing, "\t%d: ", lineno);

        printToken(currentToken, tokenString);
    }
    return currentToken;
} /* end getToken */


static void syntaxError(const char* message)
{
    fprintf(listing, "\n>>> ");
    fprintf(listing, "Syntax error at line %d: %s", lineno, message);
    Error = TRUE;
}

static void match(TokenType expected)
{
    if (token == expected) token = getToken();
    else {
        syntaxError("unexpected token -> ");
        printToken(token, tokenString);
        fprintf(listing, "      ");
    }
}

TreeNode* stmt_sequence(void)
{
    TreeNode* t = statement();
    TreeNode* p = t;
    while ((token != ENDFILE) && (token != END) &&
        (token != ELSE) && (token != UNTIL))
    {
        TreeNode* q;
        match(SEMI);
        q = statement();
        if (q != NULL) {
            if (t == NULL) t = p = q;
            else /* now p cannot be NULL either */
            {
                p->sibling = q;
                p = q;
            }
        }
    }
    return t;
}

TreeNode* statement(void)
{
    TreeNode* t = NULL;
    switch (token) {
    case IF: t = if_stmt(); break;
    case REPEAT: t = repeat_stmt(); break;
    case ID: t = assign_stmt(); break;
    case READ: t = read_stmt(); break;
    case WRITE: t = write_stmt(); break;
    // 增加while的状态
    case DO:
        t = while_stmt();
        break;
    default: syntaxError("unexpected token -> ");
        printToken(token, tokenString);
        token = getToken();
        break;
    } /* end case */
    return t;
}

TreeNode* if_stmt(void)
{
    TreeNode* t = newStmtNode(IfK);
    match(IF);
    if (t != NULL) t->child[0] = exp();
    match(THEN);
    if (t != NULL) t->child[1] = stmt_sequence();
    if (token == ELSE) {
        match(ELSE);
        if (t != NULL) t->child[2] = stmt_sequence();
    }
    match(END);
    return t;
}

TreeNode* repeat_stmt(void)
{
    TreeNode* t = newStmtNode(RepeatK);
    match(REPEAT);
    if (t != NULL) t->child[0] = stmt_sequence();
    match(UNTIL);
    if (t != NULL) t->child[1] = exp();
    return t;
}

TreeNode* assign_stmt(void)
{
    TreeNode* t = newStmtNode(AssignK);
    if ((t != NULL) && (token == ID))
        t->attr.name = copyString(tokenString);
    match(ID);
    match(ASSIGN);
    if (t != NULL) t->child[0] = exp();
    return t;
}

TreeNode* read_stmt(void)
{
    TreeNode* t = newStmtNode(ReadK);
    match(READ);
    if ((t != NULL) && (token == ID))
        t->attr.name = copyString(tokenString);
    match(ID);
    return t;
}

TreeNode* write_stmt(void)
{
    TreeNode* t = newStmtNode(WriteK);
    match(WRITE);
    if (t != NULL) t->child[0] = exp();
    return t;
}

TreeNode* while_stmt(void)
{
    TreeNode* t = newStmtNode(WhileK);
    match(DO);
    if (t != NULL) {
        t->child[0] = stmt_sequence(); // 执行语句块
    }
    match(WHILE); // 确保匹配到WHILE
    if (t != NULL) {
        t->child[1] = exp(); // 表达式条件
    }
    return t;
}



TreeNode* exp(void)
{
    TreeNode* t = simple_exp();
    if ((token == LT) || (token == EQ) || (token == GT) || (token == LTE) || (token == GTE)) { 
        TreeNode* p = newExpNode(OpK);
        if (p != NULL) {
            p->child[0] = t;
            p->attr.op = token;
            t = p;
        }
        match(token);
        if (t != NULL)
            t->child[1] = simple_exp();
    }
    return t;
}

TreeNode* simple_exp(void)
{
    TreeNode* t = term();
    while ((token == PLUS) || (token == MINUS))
    {
        TreeNode* p = newExpNode(OpK);
        if (p != NULL) {
            p->child[0] = t;
            p->attr.op = token;
            t = p;
            match(token);
            t->child[1] = term();
        }
    }
    return t;
}

TreeNode* term(void)
{
    TreeNode* t = factor();
    while ((token == TIMES) || (token == OVER))
    {
        TreeNode* p = newExpNode(OpK);
        if (p != NULL) {
            p->child[0] = t;
            p->attr.op = token;
            t = p;
            match(token);
            p->child[1] = factor();
        }
    }
    return t;
}

TreeNode* factor(void)
{
    TreeNode* t = NULL;
    switch (token) {
    case NUM:
        t = newExpNode(ConstK);
        if ((t != NULL) && (token == NUM))
            t->attr.val = atoi(tokenString);
        match(NUM);
        break;
    case ID:
        t = newExpNode(IdK);
        if ((t != NULL) && (token == ID))
            t->attr.name = copyString(tokenString);
        match(ID);
        break;
    case LPAREN:
        match(LPAREN);
        t = exp();
        match(RPAREN);
        break;
    case STRING:
        t = newExpNode(ConstK);
        t->type = String;
        if (t!=NULL&&token==STRING)
        {
            t->attr.name = copyString(tokenString);
        }
        match(STRING);
        break;
    case DOUBLE:
        t = newExpNode(ConstK);
        t->type = Double;
        if (t!=NULL&&token==DOUBLE)
        {
            t->attr.name = copyString(tokenString);
        }
        match(DOUBLE);
        break;
    case BOOL:
        t = newExpNode(ConstK);
        t->type = Boolean;
        if (t!=NULL&&token==BOOL)
        {
            t->attr.name = copyString(tokenString);
        }
        match(BOOL);
        break;
    default:
        syntaxError("unexpected token -> ");
        printToken(token, tokenString);
        token = getToken();
        break;
    }
    return t;
}

/****************************************/
/* the primary function of the parser   */
/****************************************/
/* Function parse returns the newly
 * constructed syntax tree
 */
TreeNode* parse(void)
{
    TreeNode* t = newStmtNode(StartK);
    token = getToken();
    if (token == INT||token == BOOL||token == STRING||token == FLOAT||token == DOUBLE)
    {
        t->child[0] = declaration();
        TreeNode *p = t->child[0];
        while (p->sibling!=NULL)
        {
            p=p->sibling;
        }
        p->sibling = stmt_sequence();
    }
    else
    {
        t->child[0]=stmt_sequence();
    }
    if (token != ENDFILE)
        syntaxError("Code ends before file\n");
    return t;
}
TreeNode* declaration()
{
    TreeNode* t = decl();
    TreeNode* p = t;
    while (token == INT||token == BOOL||token == STRING||token == FLOAT||token == DOUBLE)
    {
        TreeNode* q;
        q = decl();
        if (q != NULL)
        {
            if (t == NULL)
            {
                t = p = q;
            }
            else
            {
                p->sibling = q;
                p = q;
            }
        }
    }
    return t;
}
TreeNode *decl(void)
{
    TreeNode* t = newStmtNode(DeclK);
    t->attr.name = copyString(tokenString);
    token = getToken();
    if (t!=NULL)
    {
        t->child[0] = varlist();
    }
    else
    {
        syntaxError("declaration missing var"); //报出语法错误
        printToken(token,tokenString);
    }
    return t;
}
TreeNode *varlist(void)
{
    TreeNode* t=newExpNode(IdK);
    t=newExpNode(IdK);
    if ((t!=NULL)&&(token==ID))
        t->attr.name = copyString(tokenString);
    match(ID);
    if (token == COMMA)
    {
        match(COMMA);
        t->child[0]=varlist();
    }
    else if (token==SEMI)
    {
        match(SEMI);
    }
    else
    {
        syntaxError("declaration missing ';'");
        printToken(token,tokenString);
    }
    return t;
}
/* allocate global variables */
int lineno = 0;
FILE* source;
FILE* listing;
FILE* code;

/* allocate and set tracing flags */
int EchoSource = FALSE;
int TraceScan = FALSE;
int TraceParse = TRUE;

int Error = FALSE;

int main(int argc, char* argv[])
{
    TreeNode* syntaxTree;
    const char* pgm = "tiny+.txt";

    source = fopen(pgm, "r");    ////stdio
    if (source == NULL)
    {
        fprintf(stderr, "File %s not found\n", pgm);
        exit(1);
    }
    listing = stdout; /* send listing to screen */
    fprintf(listing, "\nTINY COMPILATION: %s\n", pgm);
#if NO_PARSE
    while (getToken() != ENDFILE);
#else
    syntaxTree = parse();
    if (TraceParse) {
        fprintf(listing, "Syntax tree:\n");
        printTree(syntaxTree);
    }
#endif
    fclose(source);

    return 0;
}
